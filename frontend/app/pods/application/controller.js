import Controller from '@ember/controller';
import { inject as service } from '@ember/service';
import { computed, observer } from '@ember/object';

export default Controller.extend({
  i18n: service(),
  cookies: service(),

  init() {
    this._super(...arguments);
    this.locales = ['en', 'lv', 'ru', 'ua'];
  },

  locale: computed(function() {
    const locale = this.get('cookies').read('locale');

    if (locale) {
      this.setLocale(locale);
      return locale;
    }

    return 'en';
  }),

  setLocale(locale) {
    const date = new Date();
    date.setFullYear(date.getFullYear() + 1);

    this.get('cookies').write('locale', locale, {
      path: '/',
      expires: date,
    });
    this.set('i18n.locale', locale);
  },

  changeLocale: observer('locale', function() {
    const locale = this.get('locale');
    this.setLocale(locale);
  }),
});
