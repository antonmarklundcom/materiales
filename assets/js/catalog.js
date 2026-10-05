/* Progressive catalog filter. All material URLs remain in the server-rendered HTML. */
(function () {
  'use strict';
  var controls = document.querySelector('[data-catalog-filter]');
  var catalog = document.querySelector('[data-catalog]');
  if (!controls || !catalog) return;
  var query = controls.querySelector('input');
  var status = controls.querySelector('[data-catalog-status]');
  var empty = controls.querySelector('[data-catalog-empty]');
  var items = catalog.querySelectorAll('[data-catalog-item]');
  var groups = catalog.querySelectorAll('[data-catalog-group]');
  function normalize(text) {
    return String(text).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  }
  function filter() {
    var words = normalize(query.value).trim().split(/\s+/).filter(Boolean);
    var count = 0;
    items.forEach(function (item) {
      var text = normalize(item.getAttribute('data-catalog-terms'));
      item.hidden = !words.every(function (word) { return text.indexOf(word) !== -1; });
      if (!item.hidden) count++;
    });
    groups.forEach(function (group) {
      group.hidden = !Array.prototype.some.call(group.querySelectorAll('[data-catalog-item]'), function (item) { return !item.hidden; });
    });
    status.textContent = count + (count === 1 ? ' material' : ' materiales') + ' de ' + items.length;
    empty.hidden = count !== 0;
  }
  controls.hidden = false;
  query.addEventListener('input', filter);
  controls.querySelector('[data-catalog-reset]').addEventListener('click', function () {
    query.value = '';
    filter();
    query.focus();
  });
  query.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') { query.value = ''; filter(); }
  });
  filter();
})();
