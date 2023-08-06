function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amplitude = 50; // Change this value to adjust the height of the curve

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();

    const currentDate = new Date();
    const timeStart = currentDate.getTime();
    const duration = 2 * 24*3600*1000;  //two days hence
    const timeEnd = timeStart + duration;
    
    for (let x = timeStart; x < timeEnd; x += 1*3600) {
      const y = amplitude * tideHeight(x) + canvas.height/2;  // To center the curve vertically
      ctx.lineTo((x - timeStart)*canvas.width/duration, y);
    }

    ctx.strokeStyle = '#000';
    ctx.stroke();
}

// Call the function to draw the curve
drawCurve();
