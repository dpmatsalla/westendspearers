// convert date & time in Brisbane timezone UTC+10
function formatDay(t) {
  const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

  const date = new Date(t);
  const dayOfWeek = daysOfWeek[date.getDay()];
  const month = months[date.getMonth()];
  const day = date.getDate();

  return `${dayOfWeek} ${day} ${month}`;
}

function formatDate(t) {
  const options = {
    timeZone: 'Australia/Brisbane',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  };
  const dateParts = new Date(t).toLocaleDateString('en-US', options).split('/');
  const formattedDate = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
  return formattedDate;
}
function formatTime(t) {
  const options = {
    timeZone: 'Australia/Brisbane',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false, // Set to 24-hour
  };  
  const formattedTime = new Date(t).toLocaleString('en-US', options);
  return formattedTime;
}

// determine tide at a given timestamp - incoming/outoing
function tideText(t) {
    let t0 = tideHeight(t - 0.75*3600*1000);  //at 4:15
    let t1 = tideHeight(t + 0.5*3600*1000);   //at 5:30
    let t2 = tideHeight(t + 1.75*3600*1000);  //at 6:45
    let text = t1.toFixed(1) + ' m ';

    if (t1 > t0) {
        if (t2 > t1) {text += 'incoming';}
        else {text += '↑ neutral';}
    } else {
        if (t2 < t1) {text += 'outgoing';}
        else {text += '↓ neutral';}
    }
    return text;
}

// Adjust tides for river current and add timestamp
function adjustTides() {
    var tideTime;
    var adjust = 0.0; //This is how much we should adjust the time in hours
    for (var i=0; i < tide_list.length; i++) {
        tideTime = Date.parse(tide_list[i].time_local);
        if (tide_list[i].tide === 'HIGH') { tideTime -= adjust*3600*1000; }
        else { tideTime += adjust*3600*1000; }

        tide_list[i].timestamp = tideTime;
        tide_list[i].time_local = new Date(tideTime).toISOString();
    }
}

// return height for a timestamp
function tideHeight(t) {
    var i = 0;
    while (tide_list[i].timestamp < t) {
        i++;
    }
    var t0 = tide_list[i-1].timestamp;
    var t1 = tide_list[i].timestamp;
    var h0 = parseFloat(tide_list[i-1].height);
    var h1 = parseFloat(tide_list[i].height);
    return (h0-h1)/2*Math.cos(Math.PI*(t0-t)/(t0-t1)) + (h0+h1)/2;
}

// Generate calendar and events
function generateCalendar() {
    const tableBody = document.querySelector("#calendar tbody");
    let currentDate = new Date();
    currentDate.setHours(5,0,0,0);
    const today5am = currentDate.getTime();  //5am in timestamp
    currentDate.setDate(currentDate.getDate() - currentDate.getDay());  //set to the last Sunday
    
    // Get today's date in "YYYY-MM-DD"
    const todayDate = formatDate(today5am);
    const brisbane = [-27.4698, 153.0251]; // Latitude and Longitude of Brisbane

    // Generate 30 weeks of calendar
    for (let week = 0; week < 30; week++) {
        const row = document.createElement("tr");

        for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
            var goodday = false;
            var cell = document.createElement("td");
            var cellDate = formatDate(currentDate.getTime()); // Format: "YYYY-MM-DD"
            cell.textContent = currentDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
    
            // Update tide on this date at 5am
            var tide = tideText(currentDate.getTime());
            const eventsContainer = document.createElement("div");
            const eventElement = document.createElement("div");
            eventElement.textContent = tide;
            if (tide.includes('incoming')) { 
                eventElement.classList.add('incoming');
                // good paddle day?  If yes, make cyan
                var height = tideHeight(currentDate.getTime() + 0.5*3600*1000);  //at 5:30
                if (height > 1.7 && height < 2.2) {
                    goodday = true;
                }
            }
            else if (tide.includes('outgoing')) { 
                eventElement.classList.add('outgoing'); 
            }
            else { 
                eventElement.classList.add('neutral'); 
            }
            eventsContainer.appendChild(eventElement);
            cell.appendChild(eventsContainer);
            
            // Check if there are any events on this date
            var matchingEvents = events.filter(event => event.date === cellDate);
            if (matchingEvents.length > 0) {
                const eventsContainer = document.createElement("div");
                matchingEvents.forEach(eventInfo => {
                    const eventElement = document.createElement("div");
                    eventElement.textContent = eventInfo.event;
                    eventsContainer.appendChild(eventElement);
                });
                cell.appendChild(eventsContainer);
            }
            
            //check for scratch cheese
            if (currentDate.getDay() == 5 && currentDate.getDate() < 8) {
                const eventElement = document.createElement("div");
                eventElement.textContent = 'Scratch';
                cell.appendChild(eventElement);
            }
            
            // Add today's date style
            if (cellDate === todayDate) {
                cell.classList.add("today");
            } else if (goodday) {
                cell.classList.add("goodday");
            } else {
                //get sunrise and color cell appropriately (if not already colored by goodday)
                var times = SunCalc.getTimes(currentDate, ...brisbane);
                const options = {
                    timeZone: 'Australia/Brisbane',
                    hour: '2-digit',
                    hour12: false, // Set to 24-hour
                };  
                const sunriseHr = Number(new Date(times.sunrise.getTime()).toLocaleString('en-US', options));
                if (sunriseHr >= 6) { cell.classList.add("nightpaddle"); }
                else if (sunriseHr < 5) { cell.classList.add("daypaddle"); }
                else { cell.classList.add("dawnpaddle"); }
            }
            
    
            row.appendChild(cell);
            currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
        }
        tableBody.appendChild(row);
    }
}

adjustTides();
generateCalendar();
