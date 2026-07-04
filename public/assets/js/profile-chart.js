(() => {
  const depthCanvas = document.getElementById('profile-chart');
  const rateCanvas = document.getElementById('profile-rate-chart');
  if (!depthCanvas || !rateCanvas) {
    return;
  }

  const diveNumber = depthCanvas.getAttribute('data-dive-number');
  if (!diveNumber) {
    return;
  }

  fetch(`/profile/${diveNumber}`)
    .then((response) => response.json())
    .then((payload) => {
      if (!payload || !Array.isArray(payload.series)) {
        return;
      }

      drawChart(depthCanvas, {
        title: `Depth (${payload.depthUnit || 'm'})`,
        xLabel: 'Time (min)',
        yLabel: payload.depthUnit || 'm',
        series: [
          { points: payload.series, valueKey: 'depth', color: '#0d6e6e', label: 'Depth' },
          {
            points: payload.averageSeries || [],
            valueKey: 'depth',
            color: '#9ea6ad',
            label: 'Average depth',
            dashed: true,
          },
        ],
      });

      drawChart(rateCanvas, {
        title: `Rates (${payload.rateUnit || 'm/min'})`,
        xLabel: 'Time (min)',
        yLabel: payload.rateUnit || 'm/min',
        series: [
          {
            points: payload.ascentRateSeries || [],
            valueKey: 'rate',
            color: '#0a7f32',
            label: 'Ascent',
          },
          {
            points: payload.descentRateSeries || [],
            valueKey: 'rate',
            color: '#c2551a',
            label: 'Descent',
          },
        ],
      });
    });

  function drawChart(canvas, config) {
    const ctx = canvas.getContext('2d');
    if (!ctx) {
      return;
    }

    const width = canvas.width || 700;
    const height = canvas.height || 220;
    const padding = { top: 26, right: 16, bottom: 32, left: 48 };
    const innerWidth = width - padding.left - padding.right;
    const innerHeight = height - padding.top - padding.bottom;

    const allPoints = config.series.flatMap((line) => line.points || []);
    if (allPoints.length === 0) {
      return;
    }

    const maxX = Math.max(...allPoints.map((point) => Number(point.minute || 0)), 1);
    const maxY = Math.max(...allPoints.map((point) => Number(point[config.series[0].valueKey] || 0)), 1);
    const safeMaxY = Math.max(
      maxY,
      ...config.series.flatMap((line) => (line.points || []).map((point) => Number(point[line.valueKey] || 0))),
      1,
    );

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, width, height);

    ctx.strokeStyle = '#d6dce1';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(padding.left, padding.top);
    ctx.lineTo(padding.left, height - padding.bottom);
    ctx.lineTo(width - padding.right, height - padding.bottom);
    ctx.stroke();

    ctx.fillStyle = '#46515b';
    ctx.font = '12px sans-serif';
    ctx.fillText(config.title, padding.left, 14);
    ctx.fillText(config.xLabel, width - padding.right - 70, height - 8);
    ctx.fillText(config.yLabel, 8, padding.top + 2);

    config.series.forEach((line) => {
      if (!Array.isArray(line.points) || line.points.length < 1) {
        return;
      }

      ctx.save();
      ctx.strokeStyle = line.color;
      ctx.lineWidth = 2;
      if (line.dashed) {
        ctx.setLineDash([6, 4]);
      } else {
        ctx.setLineDash([]);
      }

      ctx.beginPath();
      line.points.forEach((point, index) => {
        const minute = Number(point.minute || 0);
        const value = Number(point[line.valueKey] || 0);
        const x = padding.left + (minute / maxX) * innerWidth;
        const y = height - padding.bottom - (value / safeMaxY) * innerHeight;

        if (index === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      });
      ctx.stroke();
      ctx.restore();
    });
  }
})();
