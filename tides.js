//find where 5am is in the forecast array.  If outside array, then returns 0
function item(t) {
    var i5am=0;
    while(new Date(forecast.hourly.time[i5am]).getTime() < t && i5am < forecast.hourly.time.length) {
        i5am++;
    }
    if (i5am == forecast.hourly.time.length) {
        i5am = 0;
    }
    return i5am;
}

function drawCurve() {
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const amp = 80; // amplitude
    const xx = canvas.width;
    const yy = canvas.height - 30;

    const day = 24*3600*1000;
    const currentDate = new Date();
    const now = currentDate.getTime();
    const timeStart = now - 7*day;  //1 week ago
    const days = 20;
    const duration = days*day;
    const timeEnd = timeStart + duration;

    // get next midnight & noon & 5am
    let nextTime = new Date(timeStart);
    nextTime.setUTCHours(14, 0, 0, 0);  //2pm UTC = midnight Brisbane (next day)
    const midnight = nextTime.getTime();
    nextTime = new Date(timeStart);
    nextTime.setUTCHours(2,0,0,0);   //2am UTC = noon Brisbane
    let noon = nextTime.getTime();
    if (noon <= timeStart) {
        noon += day;
    }
    nextTime = new Date(timeStart);
    nextTime.setUTCHours(19,0,0,0);   //7pm UTC = 5am Brisbane
    let next5am = nextTime.getTime();
    if (next5am <= timeStart) {
        next5am += day;
    }

    ctx.clearRect(0, 0, xx, yy);   //clear canvas

    //draw day and night
    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane
    nextTime.setDate(nextTime.getDate() - 1);
    var times = SunCalc.getTimes(nextTime, ...brisbane);
    var sunset1 = times.sunset;  //previous sunset
    var sunsettime1 = sunset1.getTime();
    for (var i=0; i<=days; i++) {
        nextTime.setDate(nextTime.getDate() + 1);
        
        //calculate & draw current sunrise and next sunset
        times = SunCalc.getTimes(nextTime, ...brisbane);
        var sunrise = times.sunrise;
        var sunrisetime = sunrise.getTime();
        var sunset2 = times.sunset;
        var sunsettime2 = sunset2.getTime();
        ctx.fillStyle = "#BBBBBB"; //draw night period
        ctx.fillRect((sunsettime1 - timeStart)*xx/duration, 0, (sunrisetime - sunsettime1)*xx/duration, canvas.height);
        ctx.fillStyle = "#FFFFFF"; //draw day period
        ctx.fillRect((sunrisetime - timeStart)*xx/duration, 0, (sunsettime2 - sunrisetime)*xx/duration, canvas.height);

        //write sunrise time
        var sunriseFormat = sunrise.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric', hour12: false });
        var sunsetFormat = sunset1.toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric', hour12: false });
        ctx.font = "14px Arial";
        ctx.textAlign = "center";
        ctx.fillStyle = 'darkcyan'; //brown
        ctx.fillText(sunriseFormat, (sunrisetime - timeStart)*xx/duration, yy + 15);
        ctx.fillText(sunsetFormat, (sunsettime1 - timeStart)*xx/duration, yy + 15);

        // draw 5-6am paddling boxes
        const options = {
            timeZone: 'Australia/Brisbane',
            hour: '2-digit',
            hour12: false, // Set to 24-hour
        };  
        const sunriseHr = Number(new Date(sunrisetime).toLocaleString('en-US', options));
        if (sunriseHr >= 6) {
            ctx.fillStyle = "#00CCCC";  // night paddle (darkish cyan)
        } else if (sunriseHr < 5) {
            ctx.fillStyle = "#FFFFd0";  // daylight paddle (yellowish)
        } else {
            ctx.fillStyle = "#DDDFFF";  // dawn/sunrise paddle (purplish)
        }
        let t = next5am + (i-1)*day; //start at 5am
        let x = (t - timeStart)*xx/duration
        let y = yy - tideHeight(t)*amp;
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, y);
        t += 1*3600*1000;           //add an hour to bring it to 6am
        x = (t - timeStart)*xx/duration
        y = yy - tideHeight(t)*amp;
        ctx.lineTo(x, y);
        ctx.lineTo(x, 0);
        ctx.closePath();
        ctx.fill();
        ctx.fillRect((t - 3*3600*1000 - timeStart)*xx/duration, 2, (5*3600*1000)*xx/duration, yy - 3*amp - 10);
        ctx.fillRect((t - 4*3600*1000 - timeStart)*xx/duration, yy - 3*amp, (7*3600*1000)*xx/duration, 38);

        sunsettime1 = sunsettime2;
        sunset1 = sunset2;
    }

    // draw horizontal lines 
    ctx.beginPath();
    ctx.strokeStyle = 'grey';
    ctx.lineWidth = 1;
    ctx.rect(0, yy - 3*amp, xx, 2*amp);
    ctx.rect(0, yy - 2*amp, xx, 2*amp);
    ctx.stroke();

    // draw scale of tide heights on left of graph
    ctx.font = "18px Arial";
    ctx.textAlign = "left";
    ctx.fillStyle = 'gray';
    ctx.fillText("3 m", 5, yy - 3*amp +18);
    ctx.fillText("2 m", 5, yy - 2*amp +18);
    ctx.fillText("1 m", 5, yy - amp +18);

    for (i=0; i<days; i++) {
        // draw midnight ticks
        ctx.beginPath();
        ctx.strokeStyle = 'darkcyan';
        ctx.lineWidth = 2;
        let x = (midnight + i*day - timeStart)*xx/duration;
        ctx.moveTo(x, yy - 3*amp);
        ctx.lineTo(x, yy - 3*amp + 15);
        ctx.stroke();
        ctx.moveTo(x, yy);
        ctx.lineTo(x, yy - 15);
        ctx.stroke();
        ctx.lineWidth = 0.5;

        // draw noon ticks 
        x = (noon + i*day - timeStart)*xx/duration;
        ctx.moveTo(x, yy - 3*amp);  
        ctx.lineTo(x, yy - 3*amp + 15);
        ctx.stroke();
        ctx.moveTo(x, yy);  
        ctx.lineTo(x, yy - 15);
        ctx.stroke();
    
        // write text for hours
        ctx.font = "14px Arial";
        ctx.textAlign = "center";
        ctx.fillStyle = 'darkcyan';
        ctx.fillText("00:00", (midnight + i*day - timeStart)*xx/duration, yy + 15);
        ctx.fillText("12:00", (noon + i*day - timeStart)*xx/duration, yy + 15);

        // write text for date
        ctx.font = "bold 22px Arial";
        ctx.textAlign = "center";
        var nextDate = formatDay(noon + i*day);
        ctx.fillText(nextDate, (noon + i*day - timeStart)*xx/duration + 5, 25); //nudge slightly right

        // write text 5-6am in box
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.fillStyle = 'black';
        let t = next5am + i*day;
        x = (t + 0.5*3600*1000 - timeStart)*xx/duration;
        ctx.fillText('5-6am', x, 15);
        
        // write text for weather info for paddling period - either black or blue (if goodday)
        var tide = tideText(t); //calc tideText for 5-6am paddle that day
        ctx.font = 'bold 14px Arial'; 
        if (tide.includes('incoming') && parseFloat(tide.substr(0,3)) >= 1.7) { ctx.fillStyle = 'blue'; } 
            else { ctx.fillStyle = 'black'; }
        ctx.fillText(tide, x, yy - 3*amp + 15);

        // write text height change in mm/hr
        if (!tide.includes('neutral')) {
            var spd = Math.round((tideHeight(t + 1*3600*1000) - tideHeight(t))*100)*10;
            if (spd > 0) { spd = '+' + spd; }
            if (Math.abs(spd) > 400) { ctx.font = 'bold 14px Arial'; ctx.fillStyle = 'blue'; } 
                else { ctx.font = '14px Arial'; ctx.fillStyle = 'black'; }
            ctx.fillText(spd + ' mm/hr', x, yy - 3*amp + 32);
        }

        // if we have weather forecast data for the day, then write out the weather forecast
        let i5am = item(next5am + i*day);
        if (i5am > 0) {
            ctx.textAlign = "center";

            //write text for temperature 
            let data = Math.round(forecast.hourly.temperature_2m[i5am]);
            if (data < 10) { ctx.font = 'bold 14px Arial'; ctx.fillStyle = 'blue'; } 
                else if (data > 23) { ctx.font = 'bold 14px Arial'; ctx.fillStyle = 'red'; } 
                else { ctx.font = '14px Arial'; ctx.fillStyle = 'black'; }
            ctx.fillText('Temp ' + data + '°C', x, 33);

            //write text for wind
            data = Math.round(forecast.hourly.windspeed_10m[i5am]);
            if (data > 9) { ctx.font = 'bold 14px Arial'; ctx.fillStyle = 'red'; } 
                else { ctx.font = '14px Arial'; ctx.fillStyle = 'black'; }
            ctx.fillText('Wind ' + data + ' kph', x, 50);

            //write text for rain probability
            data = Math.round(forecast.hourly.precipitation_probability[i5am]);
            if (data > 60) { ctx.font = 'bold 14px Arial'; ctx.fillStyle = 'red'; } 
                else { ctx.font = '14px Arial'; ctx.fillStyle = 'black'; }
            ctx.fillText('Rain ' + data + '%', x, 67);
        }
        
    }
    
    // draw text of high and low tides on graph 
    var i = 0;
    while (tide_list[i].timestamp < timeStart) {
        i++;
    }
    var j = i;
    while (tide_list[j].timestamp < timeStart + day*days) {
        j++;
    }
    for (var k=i; k<j; k++) {
        
        //draw text for tide time and height
        let t = tide_list[k].timestamp;
        let x = (t - timeStart)*xx/duration;
        let h = parseFloat(tide_list[k].height);
        let y = yy - h*amp;
        let ttext = new Date(t).toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric', hour12: false });
        let htext = '— ' + h.toFixed(1) + ' m';
        ctx.font = '14px Arial';
        ctx.textAlign = "center";
        ctx.fillStyle = 'darkcyan';
        ctx.fillText(ttext, x, y - 20);
        ctx.fillText('|', x, y - 6);
        ctx.textAlign = "left";
        ctx.fillStyle = 'black';
        ctx.fillText(htext, x + 9, y + 4);
    }
    //draw the tide height now
    let x = (now - timeStart)*xx/duration;
    let y = yy - tideHeight(now)*amp;
    let htext = tideHeight(now).toFixed(1) + ' m —';
    ctx.fillStyle = "#DDDDDD"; //rectangle
    ctx.fillRect(x - 57, y - 11, 57, 18);
    ctx.font = 'bold 14px Arial';
    ctx.textAlign = "right";
    ctx.fillStyle = 'black';
    ctx.fillText(htext, x, y + 4);

    // draw predicted tides, 15 min intervals 
    ctx.beginPath();
    for (let t = timeStart; t < timeEnd; t += 0.25*3600*1000) {
        x = (t - timeStart)*xx/duration
        y = yy - tideHeight(t)*amp;
        ctx.lineTo(x, y);
    }
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 3;
    ctx.stroke();

    // draw historical tides, 15 min intervals 
    ctx.beginPath();
    let r = 0;
    while (r < riverData.length) {
        x = (riverData[r][0]*1000 - timeStart)*xx/duration;
        y = yy - (parseFloat(riverData[r][1]) + 1)*amp;
        ctx.lineTo(x, y);
        r++;
    }
    ctx.strokeStyle = 'green';
    ctx.setLineDash([2,1]);
    ctx.lineWidth = 4;
    ctx.stroke();

    // draw now line and text
    ctx.beginPath();
    ctx.moveTo((now - timeStart)*xx/duration, yy);
    ctx.lineTo((now - timeStart)*xx/duration, yy - 3*amp);
    ctx.strokeStyle = 'red';
    ctx.setLineDash([]);
    ctx.lineWidth = 4;
    ctx.stroke();
    ctx.font = "bold 14px Arial";
    ctx.textAlign = "center";
    ctx.fillStyle = 'red';
    htext = new Date(now).toLocaleTimeString('en-US', { timeZone: 'Australia/Brisbane', hour: 'numeric', minute: 'numeric', hour12: false });
    ctx.fillText(htext, (now - timeStart)*xx/duration, yy + 20);
    ctx.fillText("Now", (now - timeStart)*xx/duration, yy - 3*amp - 5);
    //ctx.save();
    
}
