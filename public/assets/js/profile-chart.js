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

  const depthLegend = document.getElementById('profile-chart-legend');
  const rateLegend = document.getElementById('profile-rate-legend');

  const themeColors = () => {
    if (window.DivlogChartTheme && typeof window.DivlogChartTheme.colors === 'function') {
      return window.DivlogChartTheme.colors();
    }

    return {
      canvasBg: '#ffffff',
      text: '#1f2937',
      subtleText: '#5f6b76',
      axis: '#cad5df',
      grid: '#dde5eb',
      depthMain: '#0d6e6e',
      depthAverage: '#9ea6ad',
      depthFill: 'rgba(156, 185, 214, 0.55)',
      ascent: '#0a7f32',
      descent: '#c2551a',
      tooltipBg: 'rgba(24, 34, 40, 0.9)',
      tooltipText: '#f5f9fc',
    };
  };

  fetch(`/profile/${encodeURIComponent(diveNumber)}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Profile request failed with status ${response.status}`);
      }
      return response.json();
    })
    .then((payload) => {
      if (!payload || !Array.isArray(payload.series)) {
        throw new Error('Invalid profile payload');
      }

      const depthChart = createInteractiveChart(
        depthCanvas,
        {
          title: `Depth (${payload.depthUnit || 'm'})`,
          xLabel: 'Time (min)',
          yLabel: payload.depthUnit || 'm',
          invertY: true,
          series: [
            {
              points: payload.series,
              valueKey: 'depth',
              colorKey: 'depthMain',
              label: 'Depth',
              fillToSurface: true,
              fillColorKey: 'depthFill',
            },
            {
              points: payload.averageSeries || [],
              valueKey: 'depth',
              colorKey: 'depthAverage',
              label: 'Average depth',
              dashed: true,
            },
          ],
        },
        depthLegend,
        themeColors,
      );

      const rateChart = createInteractiveChart(
        rateCanvas,
        {
          title: `Rates (${payload.rateUnit || 'm/min'})`,
          xLabel: 'Time (min)',
          yLabel: payload.rateUnit || 'm/min',
          series: [
            {
              points: payload.ascentRateSeries || [],
              valueKey: 'rate',
              colorKey: 'ascent',
              label: 'Ascent',
            },
            {
              points: payload.descentRateSeries || [],
              valueKey: 'rate',
              colorKey: 'descent',
              label: 'Descent',
            },
          ],
        },
        rateLegend,
        themeColors,
      );

      window.addEventListener('themechange', () => {
        depthChart.redraw();
        rateChart.redraw();
      });
    })
    .catch(() => {
      if (depthLegend) {
        depthLegend.textContent = 'Dive profile data could not be loaded.';
      }
      if (rateLegend) {
        rateLegend.textContent = 'Rate profile data could not be loaded.';
      }
    });

  function createInteractiveChart(canvas, config, legendNode, getTheme) {
    const ctx = canvas.getContext('2d');
    if (!ctx) {
      return { redraw() {} };
    }

    const width = Number(canvas.getAttribute('width') || 700);
    const height = Number(canvas.getAttribute('height') || 220);
    const padding = { top: 28, right: 18, bottom: 34, left: 52 };
    const innerWidth = width - padding.left - padding.right;
    const innerHeight = height - padding.top - padding.bottom;

    const allPoints = config.series.flatMap((line) => line.points || []);
    if (allPoints.length === 0 || innerWidth <= 0 || innerHeight <= 0) {
      return { redraw() {} };
    }

    const minuteStops = Array.from(
      new Set(allPoints.map((point) => Number(point.minute || 0))),
    ).sort((a, b) => a - b);

    const maxX = Math.max(...minuteStops, 1);
    const maxY = Math.max(
      ...config.series.flatMap((line) =>
        (line.points || []).map((point) => Number(point[line.valueKey] || 0)),
      ),
      1,
    );
    const minY = 0;

    const baseLegend = config.series.map((line) => line.label).join(' and ');
    setLegend(legendNode, baseLegend);

    let hoverMinute = null;

    const render = () => {
      const palette = getTheme();
      ctx.clearRect(0, 0, width, height);
      ctx.fillStyle = palette.canvasBg;
      ctx.fillRect(0, 0, width, height);

      drawGrid(palette);
      drawSurfaceLine(palette);
      drawAxes(palette);
      drawSeries(palette);

      if (hoverMinute !== null) {
        drawHoverState(hoverMinute, palette);
      }
    };

    render();

    canvas.addEventListener('mousemove', (event) => {
      const bounds = canvas.getBoundingClientRect();
      if (bounds.width <= 0) {
        return;
      }

      const pointerX = ((event.clientX - bounds.left) / bounds.width) * width;
      hoverMinute = pickNearestMinute(pointerX);
      render();
    });

    canvas.addEventListener('mouseleave', () => {
      hoverMinute = null;
      render();
      setLegend(legendNode, baseLegend);
    });

    function drawGrid(palette) {
      ctx.strokeStyle = palette.grid;
      ctx.lineWidth = 1;

      for (let tick = 0; tick <= 4; tick += 1) {
        const y = padding.top + (tick / 4) * innerHeight;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(width - padding.right, y);
        ctx.stroke();
      }
    }

    function drawAxes(palette) {
      ctx.strokeStyle = palette.axis;
      ctx.lineWidth = 1;

      ctx.beginPath();
      ctx.moveTo(padding.left, padding.top);
      ctx.lineTo(padding.left, height - padding.bottom);
      ctx.lineTo(width - padding.right, height - padding.bottom);
      ctx.stroke();

      ctx.fillStyle = palette.text;
      ctx.font = '12px Inter, sans-serif';
      ctx.fillText(config.title, padding.left, 15);
      ctx.fillText(config.xLabel, width - padding.right - 74, height - 10);
      ctx.fillText(config.yLabel, 9, padding.top + 2);

      const yTicks = [0, maxY / 2, maxY];
      yTicks.forEach((value) => {
        const y = yToPx(value);
        ctx.fillStyle = palette.subtleText;
        ctx.fillText(formatValue(value), 12, y + 4);
      });
    }

    function drawSurfaceLine(palette) {
      if (!config.invertY) {
        return;
      }

      const y = yToPx(0);
      ctx.save();
      ctx.strokeStyle = palette.axis;
      ctx.lineWidth = 1.2;
      ctx.beginPath();
      ctx.moveTo(padding.left, y);
      ctx.lineTo(width - padding.right, y);
      ctx.stroke();

      ctx.fillStyle = palette.subtleText;
      ctx.font = '11px Inter, sans-serif';
      ctx.fillText('Surface (0 m)', padding.left + 8, y + 14);
      ctx.restore();
    }

    function drawSeries(palette) {
      config.series.forEach((line) => {
        const points = line.points || [];
        if (points.length === 0) {
          return;
        }

        const color = palette[line.colorKey] || line.color || palette.depthMain;
        const fillColor = palette[line.fillColorKey] || line.fillColor || palette.depthFill;
        const firstX = xToPx(Number(points[0].minute || 0));
        const lastX = xToPx(Number(points[points.length - 1].minute || 0));
        const surfaceY = yToPx(0);

        if (line.fillToSurface && config.invertY) {
          ctx.save();
          ctx.fillStyle = fillColor;
          ctx.beginPath();
          ctx.moveTo(firstX, surfaceY);

          points.forEach((point) => {
            const x = xToPx(Number(point.minute || 0));
            const y = yToPx(Number(point[line.valueKey] || 0));
            ctx.lineTo(x, y);
          });

          ctx.lineTo(lastX, surfaceY);
          ctx.closePath();
          ctx.fill();
          ctx.restore();
        }

        ctx.save();
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.setLineDash(line.dashed ? [7, 4] : []);
        ctx.beginPath();

        points.forEach((point, index) => {
          const x = xToPx(Number(point.minute || 0));
          const y = yToPx(Number(point[line.valueKey] || 0));
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

    function drawHoverState(minute, palette) {
      const x = xToPx(minute);
      const sampled = config.series.map((line) => ({
        line,
        point: findNearestPoint(line.points || [], minute),
      }));

      ctx.save();
      ctx.strokeStyle = palette.subtleText;
      ctx.lineWidth = 1;
      ctx.setLineDash([4, 4]);
      ctx.beginPath();
      ctx.moveTo(x, padding.top);
      ctx.lineTo(x, height - padding.bottom);
      ctx.stroke();
      ctx.restore();

      sampled.forEach(({ line, point }) => {
        if (!point) {
          return;
        }

        ctx.save();
        ctx.fillStyle = palette[line.colorKey] || line.color || palette.depthMain;
        ctx.beginPath();
        ctx.arc(xToPx(Number(point.minute || 0)), yToPx(Number(point[line.valueKey] || 0)), 3.5, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
      });

      const tooltipLines = [
        `${config.xLabel}: ${formatValue(minute)} min`,
        ...sampled.map(({ line, point }) => {
          const value = point ? Number(point[line.valueKey] || 0) : 0;
          return `${line.label}: ${formatValue(value)} ${config.yLabel}`;
        }),
      ];

      drawTooltip(x, tooltipLines, palette);
      setLegend(
        legendNode,
        `${formatValue(minute)} min - ${sampled
          .map(({ line, point }) => {
            const value = point ? Number(point[line.valueKey] || 0) : 0;
            return `${line.label}: ${formatValue(value)} ${config.yLabel}`;
          })
          .join(' | ')}`,
      );
    }

    function drawTooltip(anchorX, lines, palette) {
      ctx.save();
      ctx.font = '12px Inter, sans-serif';

      const tooltipWidth = Math.max(...lines.map((line) => ctx.measureText(line).width), 120) + 18;
      const tooltipHeight = lines.length * 18 + 10;

      let left = anchorX + 12;
      if (left + tooltipWidth > width - 4) {
        left = anchorX - tooltipWidth - 12;
      }

      const top = padding.top + 8;

      ctx.fillStyle = palette.tooltipBg;
      ctx.strokeStyle = palette.axis;
      ctx.lineWidth = 1;
      ctx.fillRect(left, top, tooltipWidth, tooltipHeight);
      ctx.strokeRect(left, top, tooltipWidth, tooltipHeight);

      ctx.fillStyle = palette.tooltipText;
      lines.forEach((line, index) => {
        ctx.fillText(line, left + 8, top + 17 + index * 18);
      });

      ctx.restore();
    }

    function xToPx(minute) {
      return padding.left + (minute / maxX) * innerWidth;
    }

    function yToPx(value) {
      const clamped = Math.min(Math.max(value, minY), maxY);
      if (config.invertY) {
        return padding.top + ((clamped - minY) / (maxY - minY || 1)) * innerHeight;
      }

      return height - padding.bottom - ((clamped - minY) / (maxY - minY || 1)) * innerHeight;
    }

    function pickNearestMinute(pixelX) {
      const unclampedMinute = ((pixelX - padding.left) / innerWidth) * maxX;
      const minute = Math.min(Math.max(unclampedMinute, 0), maxX);

      return minuteStops.reduce((closest, current) =>
        Math.abs(current - minute) < Math.abs(closest - minute) ? current : closest,
      );
    }

    function findNearestPoint(points, minute) {
      if (points.length === 0) {
        return null;
      }

      return points.reduce((closest, current) =>
        Math.abs(Number(current.minute || 0) - minute) < Math.abs(Number(closest.minute || 0) - minute)
          ? current
          : closest,
      );
    }

    return { redraw: render };
  }

  function setLegend(node, text) {
    if (node) {
      node.textContent = text;
    }
  }

  function formatValue(value) {
    if (!Number.isFinite(value)) {
      return '0';
    }

    return Number(value).toFixed(2).replace(/\.00$/, '');
  }
})();
