/* global Tablesort */
(function () {
  /*
   * Define custom sort. Is similar to node_modules/tablesort/src/sorts/tablesort.number.js, but has swapped sort direction.
   */
  const compareCount = function (a, b) {
    a = parseFloat(a);
    b = parseFloat(b);

    a = isNaN(a) ? 0 : a;
    b = isNaN(b) ? 0 : b;

    return b - a;
  };

  Tablesort.extend(
    'count',
    function (item) {
      return item.match(/^[-+]?(\d)*-?([,.]){0,1}-?(\d)+([E,e][-+][\d]+)?%?$/); // Number
    },
    function (a, b) {
      return compareCount(b, a);
    }
  );

  /**
   * Initialize tablesort for the table.
   */
  // eslint-disable-next-line no-new
  new Tablesort(document.getElementById('churchTable'));
}());
