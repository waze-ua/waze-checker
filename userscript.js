// ==UserScript==
// @name         WME checker
// @version      0.13
// @description  checker
// @author       ixxvivxxi
// @include      https://www.waze.com/editor*
// @include      https://www.waze.com/*/editor*
// @include      https://beta.waze.com/editor*
// @include      https://beta.waze.com/*/editor*
// @grant        none
// @downloadURL  
// @namespace    
// @require      https://greasyfork.org/scripts/24851-wazewrap/code/WazeWrap.js
// ==/UserScript==

/* eslint-disable */
"use strict";

function checkerBelarus_bootstrap() {
    let bGreasemonkeyServiceDefined = false;

    try {
        if ("object" === typeof Components.interfaces.gmIGreasemonkeyService) {
            bGreasemonkeyServiceDefined = true;
        }
    } catch (err) {
        //Ignore.
    }

    if ("undefined" === typeof unsafeWindow || !bGreasemonkeyServiceDefined) {
        (function() {
            var dummyElem = document.createElement("p");
            dummyElem.setAttribute("onclick", "return window;");
            return dummyElem.onclick();
        })();
    }

    setTimeout(startchecker, 3000);
}

checkerBelarus_bootstrap();

function startchecker() {
    const url = '';
    const identifier = 'identifier';
    const countryID = 0;
    let regions = [];

    function initCheckerTab() {
        injectStyles(`.ui.progress{position:relative;display:block;max-width:100%;border:none;margin:1em 0 -1.5em;-webkit-box-shadow:none;box-shadow:none;background:rgba(0,0,0,.1);padding:0;border-radius:.28571429rem}.ui.progress:first-child{margin:0 0 2.5em}.ui.progress:last-child{margin:0 0 1.5em}.ui.progress .bar{display:block;line-height:1;position:relative;width:0%;min-width:2em;background:#888;border-radius:.28571429rem;-webkit-transition:width .1s ease,background-color .1s ease;transition:width .1s ease,background-color .1s ease}.ui.progress .bar>.progress{white-space:nowrap;position:absolute;width:auto;font-size:.92857143em;top:50%;right:.5em;left:auto;bottom:auto;color:rgba(255,255,255,.7);text-shadow:none;margin-top:-.3em;font-weight:700;text-align:left}.ui.progress>.label{position:absolute;width:100%;font-size:1em;top:100%;right:auto;left:0;bottom:auto;color:rgba(0,0,0,.87);font-weight:700;text-shadow:none;margin-top:.2em;text-align:center;-webkit-transition:color .4s ease;transition:color .4s ease}.ui.indicating.progress[data-percent^="1"] .bar,.ui.indicating.progress[data-percent^="2"] .bar{background-color:#d95c5c}.ui.indicating.progress[data-percent^="3"] .bar{background-color:#efbc72}.ui.indicating.progress[data-percent^="4"] .bar,.ui.indicating.progress[data-percent^="5"] .bar{background-color:#e6bb48}.ui.indicating.progress[data-percent^="6"] .bar{background-color:#ddc928}.ui.indicating.progress[data-percent^="7"] .bar,.ui.indicating.progress[data-percent^="8"] .bar{background-color:#b4d95c}.ui.indicating.progress[data-percent^="100"] .bar,.ui.indicating.progress[data-percent^="9"] .bar{background-color:#66da81}.ui.indicating.progress[data-percent^="1"] .label,.ui.indicating.progress[data-percent^="2"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent^="3"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent^="4"] .label,.ui.indicating.progress[data-percent^="5"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent^="6"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent^="7"] .label,.ui.indicating.progress[data-percent^="8"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent^="100"] .label,.ui.indicating.progress[data-percent^="9"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress[data-percent="1"] .bar,.ui.indicating.progress[data-percent="2"] .bar,.ui.indicating.progress[data-percent="3"] .bar,.ui.indicating.progress[data-percent="4"] .bar,.ui.indicating.progress[data-percent="5"] .bar,.ui.indicating.progress[data-percent="6"] .bar,.ui.indicating.progress[data-percent="7"] .bar,.ui.indicating.progress[data-percent="8"] .bar,.ui.indicating.progress[data-percent="9"] .bar{background-color:#d95c5c}.ui.indicating.progress[data-percent="1"] .label,.ui.indicating.progress[data-percent="2"] .label,.ui.indicating.progress[data-percent="3"] .label,.ui.indicating.progress[data-percent="4"] .label,.ui.indicating.progress[data-percent="5"] .label,.ui.indicating.progress[data-percent="6"] .label,.ui.indicating.progress[data-percent="7"] .label,.ui.indicating.progress[data-percent="8"] .label,.ui.indicating.progress[data-percent="9"] .label{color:rgba(0,0,0,.87)}.ui.indicating.progress.success .label{color:#1a531b}.ui.progress.success .bar{background-color:#21ba45!important}.ui.progress.success .bar,.ui.progress.success .bar::after{-webkit-animation:none!important;animation:none!important}.ui.progress.success>.label{color:#1a531b}.ui.progress.warning .bar{background-color:#f2c037!important}.ui.progress.warning .bar,.ui.progress.warning .bar::after{-webkit-animation:none!important;animation:none!important}.ui.progress.warning>.label{color:#794b02}.ui.progress.error .bar{background-color:#db2828!important}.ui.progress.error .bar,.ui.progress.error .bar::after{-webkit-animation:none!important;animation:none!important}.ui.progress.error>.label{color:#912d2b}.ui.active.progress .bar{position:relative;min-width:2em}.ui.active.progress .bar::after{content:'';opacity:0;position:absolute;top:0;left:0;right:0;bottom:0;background:#fff;border-radius:.28571429rem;-webkit-animation:progress-active 2s ease infinite;animation:progress-active 2s ease infinite}@-webkit-keyframes progress-active{0%{opacity:.3;width:0}100%{opacity:0;width:100%}}@keyframes progress-active{0%{opacity:.3;width:0}100%{opacity:0;width:100%}}.ui.disabled.progress{opacity:.35}.ui.disabled.progress .bar,.ui.disabled.progress .bar::after{-webkit-animation:none!important;animation:none!important}.ui.inverted.progress{background:rgba(255,255,255,.08);border:none}.ui.inverted.progress .bar{background:#888}.ui.inverted.progress .bar>.progress{color:#f9fafb}.ui.inverted.progress>.label{color:#fff}.ui.inverted.progress.success>.label{color:#21ba45}.ui.inverted.progress.warning>.label{color:#f2c037}.ui.inverted.progress.error>.label{color:#db2828}.ui.progress.attached{background:0 0;position:relative;border:none;margin:0}.ui.progress.attached,.ui.progress.attached .bar{display:block;height:.2rem;padding:0;overflow:hidden;border-radius:0 0 .28571429rem .28571429rem}.ui.progress.attached .bar{border-radius:0}.ui.progress.top.attached,.ui.progress.top.attached .bar{top:0;border-radius:.28571429rem .28571429rem 0 0}.ui.progress.top.attached .bar{border-radius:0}.ui.card>.ui.attached.progress,.ui.segment>.ui.attached.progress{position:absolute;top:auto;left:0;bottom:100%;width:100%}.ui.card>.ui.bottom.attached.progress,.ui.segment>.ui.bottom.attached.progress{top:100%;bottom:auto}.ui.red.progress .bar{background-color:#db2828}.ui.red.inverted.progress .bar{background-color:#ff695e}.ui.orange.progress .bar{background-color:#f2711c}.ui.orange.inverted.progress .bar{background-color:#ff851b}.ui.yellow.progress .bar{background-color:#fbbd08}.ui.yellow.inverted.progress .bar{background-color:#ffe21f}.ui.olive.progress .bar{background-color:#b5cc18}.ui.olive.inverted.progress .bar{background-color:#d9e778}.ui.green.progress .bar{background-color:#21ba45}.ui.green.inverted.progress .bar{background-color:#2ecc40}.ui.teal.progress .bar{background-color:#00b5ad}.ui.teal.inverted.progress .bar{background-color:#6dffff}.ui.blue.progress .bar{background-color:#2185d0}.ui.blue.inverted.progress .bar{background-color:#54c8ff}.ui.violet.progress .bar{background-color:#6435c9}.ui.violet.inverted.progress .bar{background-color:#a291fb}.ui.purple.progress .bar{background-color:#a333c8}.ui.purple.inverted.progress .bar{background-color:#dc73ff}.ui.pink.progress .bar{background-color:#e03997}.ui.pink.inverted.progress .bar{background-color:#ff8edf}.ui.brown.progress .bar{background-color:#a5673f}.ui.brown.inverted.progress .bar{background-color:#d67c1c}.ui.grey.progress .bar{background-color:#767676}.ui.grey.inverted.progress .bar{background-color:#dcddde}.ui.black.progress .bar{background-color:#1b1c1d}.ui.black.inverted.progress .bar{background-color:#545454}.ui.tiny.progress{font-size:.85714286rem}.ui.tiny.progress .bar{height:.5em}.ui.small.progress{font-size:.92857143rem}.ui.small.progress .bar{height:1em}.ui.progress{font-size:1.3rem}.ui.progress .bar{height:1.75em}.ui.large.progress{font-size:1.14285714rem}.ui.large.progress .bar{height:2.5em}.ui.big.progress{font-size:1.28571429rem}.ui.big.progress .bar{height:3.5em}`);

        // let tabsContent = $("#user-info .tab-content");
        // tabsContent.append(`<div class="tab-pane" id="checker-${identifier}"></div>`);
        //
        // let tabs = $("#user-tabs> ul.nav-tabs");
        // tabs.append(`<li><a href="#checker-${identifier}" id="checker-tab-${identifier}" data-toggle="tab">Checker ${identifier}</a></li>`);
        //
        // let checkerContent = $(`#checker-${identifier}`);
        //
        // let resultContainer = $('<div class="result-list-container"></div>');
        // let resultList = $('<div class="result-list"></div>');
        //
        // resultContainer.append(resultList);
        // checkerContent.append(resultContainer);

        let tabContent = `\
          <div id="checker-${identifier}">
            <div class="result-list-container">
              <div class="result-list"></div>
            </div>
            <div>
              <button class="btn btn-default" id="checker-${identifier}-update-all">
                Обновить все
              </button>
            </div>
          </div>
        `;
        new WazeWrap.Interface.Tab(`Checker ${identifier}`, tabContent);

        showRegions();
    }

    function showRegions() {
        let resultList = $(`#checker-${identifier}>.result-list-container>.result-list`);
        resultList.empty();
         $.ajax(url + "api/regions").done(function(response) {
            regions = response.data.map(item => item.id);
            response.data.forEach(item => {
                let lastUpdate = new Date(+item.attributes['last-update']).toLocaleString();
                resultList.append(`
                  <li class="result">
                    <p class="title">${item.attributes.name}</p>
                    <p class="additional-info clearfix">${lastUpdate}</p>
                    <p class="additional-info clearfix">
                      <button class="btn btn-default btn-checker-${identifier}" region="${item.id}">
                        Обновить
                      </button>
                    </p>
                    <p class="additional-info clearfix">
                      <div id="progress-checker-${identifier}-${item.id}" class="ui indicating progress active" data-percent="0" style="display:none;">
                        <div class="bar" style="transition-duration: 300ms; width: 0px;">
                          <div class="progress"></div>
                        </div>
                     </div>
                   </p>
                 </li>
               `);
            });
        });

    }

    function getBoxes(region) {
        return httpGet(`${url}api/bboxes?region=${region}`).then(response => response.json());
    }

    async function updateData(bboxes, region) {

        $(`.btn-checker-${identifier}`).each(function(index, element)  {
            $(element).attr('disabled', true);
        });

        let cities = [];
        let users = [];

        await httpGet(`${url}api/regions/methods/prepaireData/${region}`);

        let progressEl = $(`#progress-checker-${identifier}-${region}`);
        progressEl.removeClass('success');
        progressEl.show();

        let count = bboxes.data.length;

        await asyncForEach(bboxes.data, async (item, index) => {
            let box = item.attributes;
            let data = await httpGet(
                `/row-Descartes/app/Features?
                  language=en&bbox=${box.west},${box.south},${box.east},${box.north}
                  &roadTypes=1,2,3,4,6,7,8,9,11,12,13,14,15,17,20,21,22`)
                .then(response => response.json());

            // let data = JSON.parse(json);
            await setConnections(data.connections);
            await setSegments(data.segments.objects, region);
            await setStreets(data.streets.objects);

            cities.push(...data.cities.objects);
            users.push(...data.users.objects);

            if (index === count - 1) {
                await setCities(cities);
                await setUsers(users);
                $(`.btn-checker-${identifier}`).each(function(index, element) {
                    $(element).removeAttr('disabled');
                });

                await httpGet(`${url}api/regions/methods/finishData/${region}/${countryID}`);
                progressEl.addClass('success');
            }

            let percent = parseInt((index + 1) / count * 100);

            progressEl.attr('data-percent', percent);
            progressEl.find('.bar').css({'width': percent + '%'});
            progressEl.find('.progress').text( percent + '%');
        });
    }

    $("#sidebar").on("click", `.btn-checker-${identifier}`, function() {
        let region = $(this).attr("region");
        let disabled = $(this).attr("disabled");

        if (!disabled) {
            updateRegion(region).then(() => {
                showRegions();
            });
        }
    });
    $("#sidebar").on("click", `#checker-${identifier}-update-all`, function() {
      updateAllRegions();
    });

    async function updateAllRegions() {
      let regions = regionsGenerator();
      while (true) {
        res = regions.next();
        if (res.done) {
          break;
        }
        await updateRegion(res.value);
      }
      showRegions();
    }

    function* regionsGenerator() {
      yield* regions;
    }

    async function updateRegion(region) {
      return getBoxes(region).then(data => {
          return updateData(data, region);
      }).catch(err => console.error(err));
    }

    async function setSegments(segments, region) {
        let segments500 = chunkArray(segments, 500);
        await asyncForEach(segments500, async (array) => {
            await httpPOST(
                `${url}api/segments/methods/setDataFromWME/${region}`,
                {data: array}
            );
        });

    }

    async function setCities(cities) {
       let cities500 = chunkArray(cities, 500);
        await asyncForEach(cities500, async (array) => {
            await httpPOST(
                `${url}api/cities/methods/setDataFromWME`,
                {data: array}
            );
        });

    }

    async function setStreets(streets) {
        let streets500 = chunkArray(streets, 500);
        await asyncForEach(streets500, async (array) => {
            await httpPOST(
                `${url}api/streets/methods/setDataFromWME`,
                {data: array}
            );
        });
    }

    async function setUsers(users) {
        let users500 = chunkArray(users, 500);
        await asyncForEach(users500, async (array) => {
            await httpPOST(
                `${url}api/users/methods/setDataFromWME`,
                {data: array}
            );
        });
    }

    async function setConnections(connections) {
        await httpPOST(
            `${url}api/connections/methods/setDataFromWME`,
            {data: connections}
        );
    }

    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    function httpGet(url) {
        return fetch(url).then(response => {
            if (response.ok) {
              return response;
            } else {
              throw new Error("HTTP GET Error. Response failed");
            }
        }).catch(err => console.error(err));
    }

    function httpPOST(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: {
              "Content-type": "application/json; charset=utf-8"
            },
            body: JSON.stringify(data)
        }).then(response => {
            if (response.ok) {
              return response;
            } else {
              throw new Error("HTTP POST Error. Response failed");
            }
        }).catch(err => console.error(err));
    }

    async function asyncForEach(array, callback) {
        for (let index = 0; index < array.length; index++) {
            await callback(array[index], index, array);
        }
    }

    function chunkArray(myArray, chunk_size){
        var results = [];

        while (myArray.length) {
            results.push(myArray.splice(0, chunk_size));
        }
        return results;
    }



    function injectStyles(rule) {
        var div = $("<div />", {
            html: '&shy;<style>' + rule + '</style>'
        }).appendTo("body");
    }

    initCheckerTab();
}
