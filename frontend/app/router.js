import EmberRouter from '@ember/routing/router';
import config from './config/environment';

const Router = EmberRouter.extend({
  location: config.locationType,
  rootURL: config.rootURL,
});

Router.map(function() {
  this.route('index', { path: '/' });

  this.route('region', { path: 'region/:region_id' }, function() {
    this.route('segments');
  });

  this.route('users');

  this.route('init', function() {
    this.route('regions', function() {
      this.route('region', { path: ':region_id' });
    });
  });

  this.route('checker')
});

export default Router;
