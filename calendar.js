// convert date & time
function formatDate(time_stamp) {
  const date = new Date(time_stamp);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
function formatTime(timestamp) {
  const date = new Date(timestamp);
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${hours}:${minutes}`;
}

// Adjust tides for current and add time_stamp
function adjustTides() {
    var tideTime;
    for (var i=0; i < tide_list.length; i++) {
        tideTime = Date.parse(tide_list[i].time_local);
        if (tide_list[i].tide === 'HIGH') { tideTime -= 0.75*3600*1000; }
        else { tideTime += 0.75*3600*1000; }

        tide_list[i].time_stamp = tideTime;
    }
}

// Function to group the tide information by date and separate low and high tides
function groupTidesByDate() {
    var tidesByDate = [];
    tide_list.forEach(function(tide) {
        var dateStr = formatDate(tide.time_stamp);
        var timeStr = formatTime(tide.time_stamp);
        if (tide.tide == 'HIGH') { tideStr = "↑"; }
        else { tideStr = "↓"; }
        //if (!tidesByDate[dateStr]) {
        //    tidesByDate[dateStr] = { 'types': [], 'times': [], 'heights': [] };
        //}
        //tidesByDate[dateStr].types.push(tide.tide);
        //tidesByDate[dateStr].times.push(timeStr);
        //tidesByDate[dateStr].heights.push(tide.height);
        tidesByDate.push({ date: dateStr, event: timeStr+": "+tideStr+" "+tide.height+"m" });
    });
    return tidesByDate;
}

// return height for a time_stamp
function tideHeight(t) {
    var i = 0;
    while (tide_list[i].time_stamp < t) {
        i++;
    }
    var t0 = tide_list[i-1].time_stamp;
    var t1 = tide_list[i].time_stamp;
    var h0 = tide_list[i-1].height;
    var h1 = tide_list[i].height;
    return (h0-h1)/2*Math.cos(Math.PI*(t0-t)/(t0-t1)) + (h0+h1)/2;
}

// Generate calendar and events
function generateCalendar() {
    const tableBody = document.querySelector("#calendar tbody");
    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() - currentDate.getDay());  //set to the last Sunday
    
    // Get tides
    const tides = groupTidesByDate(); // { date: "2023-08-01), event: "5:21: LOW 0.21 m" }
    
    // List of events
    const events = [
        { date: "2023-08-16", event: "EKKA" },
        { date: "2023-08-18", event: "Cheese" },
        { date: "2023-10-31", event: "Halloween" },
        { date: "2023-11-10", event: "AGM" },
        { date: "2023-11-11", event: "AGM" },
        { date: "2023-12-25", event: "Merry Christmas!" },
        // Add more events here...
    ];

    // Get today's date in "YYYY-MM-DD"
    const todayDate = new Date().toISOString().split("T")[0];

    // Generate 10 weeks of calendar
    for (let week = 0; week < 10; week++) {
        const row = document.createElement("tr");

        for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
            const cell = document.createElement("td");
            const cellDate = currentDate.toISOString().split("T")[0]; // Format: "YYYY-MM-DD"
            cell.textContent = currentDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
    
            // Update tides on this date
            var matchingEvents = tides.filter(event => event.date === cellDate);
            if (matchingEvents.length > 0) {
                const eventsContainer = document.createElement("div");
                matchingEvents.forEach(eventInfo => {
                    const eventElement = document.createElement("div");
                    eventElement.textContent = eventInfo.event;
                    eventsContainer.appendChild(eventElement);
                });
                cell.appendChild(eventsContainer);
            }

            // Check if there are any events on this date
            matchingEvents = events.filter(event => event.date === cellDate);
            if (matchingEvents.length > 0) {
                const eventsContainer = document.createElement("div");
                matchingEvents.forEach(eventInfo => {
                    const eventElement = document.createElement("div");
                    eventElement.textContent = eventInfo.event;
                    eventsContainer.appendChild(eventElement);
                });
                cell.appendChild(eventsContainer);
            }
            
            // Add today's date style
            if (cellDate === todayDate) {
                cell.classList.add("today");
            }
    
            row.appendChild(cell);
            currentDate.setDate(currentDate.getDate() + 1); // Move to the next day
        }
        tableBody.appendChild(row);
    }
}

adjustTides();
generateCalendar();
