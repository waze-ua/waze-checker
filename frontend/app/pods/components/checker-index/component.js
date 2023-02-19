import Component from "@ember/component";
import { get } from "@ember/object";
import { inject as service } from "@ember/service";
import { task } from "ember-concurrency";

export default Component.extend({
  ajax: service(),

  tagName: "",

  countryId: null,
  user: {},

  init() {
    this._super(...arguments);

    this.get("fetchCountyId").perform();
    this.get("checkLogin").perform();
  },

  checkLogin: task(function* () {
    const data = yield this.get("ajax").request("/checker/check-login");
    this.set("user", data);
  }),

  fetchCountyId: task(function* () {
    const data = yield this.get("ajax").request("/key-value/COUNTRY_ID");

    this.set("countryId", get(data, "keyValue.value"));
  }),

  updateCountryId: task(function* () {
    const data = yield this.get("ajax").request("/key-value", {
      method: "POST",
      data: {
        key: "COUNTRY_ID",
        value: this.countryId,
      },
    });

    this.set("countryId", get(data, "keyValue.value"));
  }),

  updateToken: task(function* () {
    const data = yield this.get("ajax").request("/checker/set-token", {
      method: "POST",
      data: {
        token: this.token,
      },
    });

    this.set("user", data);
  }),

  actions: {
    handleChangeCountryId(e) {
      this.set("countryId", e.target.value);
    },

    handleChangeToken(e) {
      this.set("token", e.target.value);
    },
  },
});
