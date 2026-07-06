(() => {
  const root = document.documentElement;

  const readColor = (name, fallback) => {
    const value = getComputedStyle(root).getPropertyValue(name).trim();
    return value || fallback;
  };

  window.DivlogChartTheme = {
    colors() {
      return {
        canvasBg: readColor('--surface', '#ffffff'),
        text: readColor('--on-surface', '#1f2937'),
        subtleText: readColor('--on-surface-variant', '#5f6b76'),
        axis: readColor('--outline-variant', '#cad5df'),
        grid: readColor('--outline', '#dde5eb'),
        depthMain: readColor('--primary', '#0d6e6e'),
        depthAverage: readColor('--tertiary', '#8a9399'),
        depthFill: readColor('--secondary-container', 'rgba(156, 185, 214, 0.55)'),
        ascent: readColor('--secondary', '#0a7f32'),
        descent: readColor('--error', '#c2551a'),
        tooltipBg: readColor('--inverse-surface', 'rgba(24, 34, 40, 0.9)'),
        tooltipText: readColor('--inverse-on-surface', '#f5f9fc'),
        legendSwatches: [
          readColor('--primary', '#0d6e6e'),
          readColor('--secondary', '#1c8f6f'),
          readColor('--tertiary', '#5aa469'),
          readColor('--error', '#c2873a'),
          readColor('--primary-container', '#b35233'),
        ],
      };
    },
  };
})();
