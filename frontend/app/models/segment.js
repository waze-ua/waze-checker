import DS from 'ember-data';

export default DS.Model.extend({
  flags: DS.attr('number'),
  lockRank: DS.attr('number'),
  lat: DS.attr('number'),
  lon: DS.attr('number'),
  length: DS.attr('number'),
  hasTransition: DS.attr('boolean'),
  updatedOn: DS.attr('string'),

  revToll: DS.attr('boolean'),
  fwdToll: DS.attr('boolean'),
  fwdDirection: DS.attr('boolean'),
  revDirection: DS.attr('boolean'),

  fwdMaxSpeed: DS.attr('number'),
  revMaxSpeed: DS.attr('number'),

  updatedBy: DS.belongsTo('user'),
  street: DS.belongsTo('street'),
  roadType: DS.belongsTo('road-type'),
});
