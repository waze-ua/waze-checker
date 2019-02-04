/* global google */
import Component from '@ember/component';
import { get, set, computed } from '@ember/object';
import { inject as service } from '@ember/service';
import { scheduleOnce } from '@ember/runloop';
import { task } from 'ember-concurrency';
import $ from 'jquery';
import ENV from 'waze-checker/config/environment';

export default Component.extend({
  map: null,
  poly: null,
  BBoxes: null,
  mapBoxes: null,

  ajax: service(),
  store: service(),
  i18n: service(),
  notifications: service('notification-messages'),

  init() {
    this._super(...arguments);
    let key = ENV.googleMapKey;
    let url = `https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,drawing&key=${key}`;
    scheduleOnce('afterRender', this, function() {
      $.getScript(url, () => {
        this._initialize();
      });
    });
  },

  didUpdateAttrs() {
    this._super(...arguments);
    this._setPolygon();
    this._clearMapBoxes();
  },

  ajaxMethods: task(function*(url, options = {}) {
    return yield get(this, 'ajax').request(url, options);
  }),

  isEnableSave: computed('BBoxes', function() {
    if (get(this, 'BBoxes')) {
      return true;
    }
    return false;
  }),

  _initialize() {
    let mapDiv = document.getElementById('map');
    let map = new google.maps.Map(mapDiv, {
      center: new google.maps.LatLng(53.791424, 27.3789596),
      mapTypeId: google.maps.MapTypeId.roadmap,
      zoom: 7,
      tilt: 0,
    });

    set(this, 'map', map);

    this._setPolygon();
  },

  _setPolygon() {
    let id = get(this, 'region.id');
    let poly = get(this, 'poly');
    if (poly) {
      poly.setMap(null);
    }
    this._clearMapBoxes();
    if (+id === 0) {
      return;
    }
    let url = `${ENV.hostname}/api/regions/methods/getPolygon/${id}`;

    get(this, 'ajaxMethods')
      .perform(url)
      .then(data => {
        let map = get(this, 'map');
        let center = map.getCenter();
        let coordinates = [
          [
            { lng: center.lng() - 0.5, lat: center.lat() },
            { lng: center.lng() + 0.5, lat: center.lat() },
            { lng: center.lng(), lat: center.lat() + 0.5 },
            { lng: center.lng() - 0.5, lat: center.lat() },
          ],
        ];
        if (data) {
          coordinates = this.convertToCoordinates(data);
        }

        poly = new google.maps.Polygon({
          paths: coordinates,
          strokeColor: '#FFC107',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FFC107',
          fillOpacity: 0.35,
          editable: true,
        });

        poly.setMap(map);

        google.maps.event.addListener(poly, 'dblclick', function(e) {
          if (e.vertex) {
            poly.getPath().removeAt(e.vertex);
          }
        });

        this._setCenterPoly(map, poly);

        set(this, 'poly', poly);
      });
  },

  _setCenterPoly(map, poly) {
    let bounds = new google.maps.LatLngBounds();

    let path = poly.getPath();

    if (path.length > 0) {
      path.forEach(latlng => {
        bounds.extend(latlng);
      });

      map.setCenter(bounds.getCenter());
    }
  },

  _clearMapBoxes() {
    let mapBoxes = get(this, 'mapBoxes');
    if (mapBoxes) {
      mapBoxes.forEach(box => {
        box.setMap(null);
      });
      set(this, 'mapBoxes', null);
    }
  },

  convertToCoordinates(data) {
    if (data.coordinates) {
      return data.coordinates.map(coordinatesGroup => {
        return coordinatesGroup.map(item => {
          return {
            lng: item[0],
            lat: item[1],
          };
        });
      });
    }

    return [];
  },

  actions: {
    savePoly() {
      let polygon = get(this, 'poly');
      let i18n = get(this, 'i18n');
      let paths = polygon.getPath();
      paths = paths.getArray();

      let id = get(this, 'region.id');
      let url = `${ENV.hostname}/api/regions/methods/savePolygon/${id}`;
      get(this, 'ajaxMethods')
        .perform(url, {
          data: JSON.stringify({
            coordinates: paths.map(item => {
              return `${Math.round(item.lng() * 10000) / 10000} ${Math.round(
                item.lat() * 10000
              ) / 10000}`;
            }),
          }),
          contentType: 'application/json; charset=utf-8',
          dataType: 'json',
          processData: false,
          method: 'POST',
        })
        .then(
          response => {
            if (get(response, 'data.status') === 'error') {
              get(this, 'notifications').error(i18n.t('messages.error'));
            } else {
              get(this, 'notifications').success(i18n.t('messages.saved'));
            }
          },
          () => {
            get(this, 'notifications').error(i18n.t('messages.error'));
          }
        );
    },

    showBBoxes() {
      let map = get(this, 'map');
      this._clearMapBoxes();

      let mapBoxes = [];

      let id = get(this, 'region.id');
      get(this, 'store')
        .query('bbox', { region: id, itemsPerPage: 0 })
        .then(bboxes => {
          let boxes = bboxes.map(item => {
            return {
              north: get(item, 'north'),
              south: get(item, 'south'),
              east: get(item, 'east'),
              west: get(item, 'west'),
            };
          });

          boxes.map(bounds => {
            mapBoxes.push(
              new google.maps.Rectangle({
                bounds,
                map: map,
                strokeColor: 'green',
                strokeOpacity: 1,
                strokeWeight: 1,
                fillColor: 'yellow',
                fillOpacity: 0.5,
                clickable: false,
              })
            );
          });
        });

      set(this, 'mapBoxes', mapBoxes);
    },

    didSelectFiles(files, resetInput) {
      let i18n = get(this, 'i18n');
      if (files.length === 1) {
        let file = files[0];
        let fd = new FormData();
        fd.append('geo_data', file);
        let region = get(this, 'region.id');
        let url = `${ENV.hostname}/api/regions/methods/uploadPolygon/${region}`;

        get(this, 'ajaxMethods')
          .perform(url, {
            data: fd,
            processData: false,
            contentType: false,
            method: 'POST',
          })
          .then(
            response => {
              if (get(response, 'data.status') === 'error') {
                get(this, 'notifications').error(i18n.t('messages.error'));
              } else {
                get(this, 'notifications').success(i18n.t('messages.saved'));
              }
            },
            () => {
              get(this, 'notifications').error(i18n.t('messages.error'));
            }
          );
      }

      resetInput();
    },
  },
});
