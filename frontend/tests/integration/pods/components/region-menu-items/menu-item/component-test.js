import { moduleForComponent, test } from 'ember-qunit';
import hbs from 'htmlbars-inline-precompile';

moduleForComponent('region-menu-items/menu-item', 'Integration | Component | region menu items/menu item', {
  integration: true
});

test('it renders', function(assert) {
  // Set any properties with this.set('myProperty', 'value');
  // Handle any actions with this.on('myAction', function(val) { ... });

  this.render(hbs`{{region-menu-items/menu-item}}`);

  assert.equal(this.$().text().trim(), '');

  // Template block usage:
  this.render(hbs`
    {{#region-menu-items/menu-item}}
      template block text
    {{/region-menu-items/menu-item}}
  `);

  assert.equal(this.$().text().trim(), 'template block text');
});
