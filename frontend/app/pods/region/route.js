import Route from '@ember/routing/route';

export default Route.extend({
  model(params) {
    return this.store.findRecord('region', params.region_id);
  },

  setupController(controller, model) {
    this._super(...arguments);
    controller.set('region', model);
  },

  afterModel(model, transition) {
    if (transition.targetName === 'region.index') {
      this.transitionTo('region.segments', model.id, {
        queryParams: { type: 'all', page: 1 },
      });
    }
  },
});
