function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amplitude = 50; // Change this value to adjust the height of the curve
    const frequency = 0.02; // Change this value to adjust the frequency of the curve
    const phaseShift = 0; // Change this value to adjust the phase shift of the curve
    const yOffset = canvas.height / 2; // To center the curve vertically

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();

    const currentDate = new Date();
    const timestamp = currentDate.getTime();
    
    for (let x = 0; x < canvas.width; x++) {
      const y = amplitude * Math.sin(frequency * x + phaseShift) + yOffset;
      ctx.lineTo(x, y);
    }

    ctx.strokeStyle = '#000';
    ctx.stroke();
}

// Call the function to draw the curve
drawCurve();
