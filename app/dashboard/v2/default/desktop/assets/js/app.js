new Chartist.Line('.chart-box', {
    labels: [1, 2, 3, 4, 5, 6, 7, 8],
    series: [
      [5, 9, 7, 8, 5, 3, 5, 4]
    ]
},
{
    fullWidth: true,
    low: 0,
    showArea: true,axisX: {
        showLabel: false,
        offset: 0
      },
      axisY: {
        showLabel: false,
        offset: 0
      }
},{
    width: '10%',
    height: 50
});