(() => {
  const canvas = document.getElementById('stats-depth-chart');
  if (!(canvas instanceof HTMLCanvasElement)) {
    return;
  }

  const raw = canvas.dataset.depthDistribution;
  if (!raw) {
    return;
  }

  let buckets;
  try {
    buckets = JSON.parse(raw);
  } catch {
    return;
  }

  if (!Array.isArray(buckets)) {
    return;
  }

  const counts = buckets.map((bucket) => Number(bucket.count || 0));
  const total = counts.reduce((sum, count) => sum + count, 0);
  const ctx = canvas.getContext('2d');
  if (!ctx) {
    return;
  }

  const width = Number(canvas.getAttribute('width') || 700);
  const height = Number(canvas.getAttribute('height') || 320);
  const dpr = window.devicePixelRatio || 1;
  canvas.width = Math.floor(width * dpr);
  canvas.height = Math.floor(height * dpr);
  canvas.style.width = `${width}px`;
  canvas.style.height = `${height}px`;
  ctx.scale(dpr, dpr);

  ctx.clearRect(0, 0, width, height);
  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, width, height);

  if (total <= 0) {
    ctx.fillStyle = '#4e6460';
    ctx.font = '16px "Source Sans 3", sans-serif';
    ctx.fillText('No depth data available', width / 2 - 70, height / 2);
    return;
  }

  const colors = ['#0d6e6e', '#1c8f6f', '#5aa469', '#c2873a', '#b35233'];
  const centerX = 180;
  const centerY = height / 2;
  const radius = 120;
  let start = -Math.PI / 2;

  buckets.forEach((bucket, index) => {
    const count = Number(bucket.count || 0);
    if (count <= 0) {
      return;
    }

    const slice = (count / total) * Math.PI * 2;
    const end = start + slice;

    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, radius, start, end);
    ctx.closePath();
    ctx.fillStyle = colors[index % colors.length];
    ctx.fill();

    const mid = start + slice / 2;
    const labelX = centerX + Math.cos(mid) * (radius * 0.65);
    const labelY = centerY + Math.sin(mid) * (radius * 0.65);
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 12px "Source Sans 3", sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(`${bucket.percent}%`, labelX, labelY);

    start = end;
  });

  ctx.textAlign = 'left';
  ctx.textBaseline = 'middle';
  ctx.font = '14px "Source Sans 3", sans-serif';

  const legendStartX = 360;
  const legendStartY = 80;
  const rowHeight = 34;

  buckets.forEach((bucket, index) => {
    const y = legendStartY + index * rowHeight;
    ctx.fillStyle = colors[index % colors.length];
    ctx.fillRect(legendStartX, y - 8, 16, 16);

    ctx.fillStyle = '#203634';
    ctx.fillText(`${bucket.label}: ${bucket.count} (${bucket.percent}%)`, legendStartX + 24, y);
  });
})();
