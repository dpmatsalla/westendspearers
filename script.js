// Function to group the tide information by date and separate low and high tides
function groupTidesByDate() {
    var tidesByDate = [];
    tide_list.forEach(function(tide) {
        var dateStr = tide.time_local.split('T')[0];
        var timeStr = tide.time_local.split('T')[1].substr(0,5);
        //if (!tidesByDate[dateStr]) {
        //    tidesByDate[dateStr] = { 'types': [], 'times': [], 'heights': [] };
        //}
        //tidesByDate[dateStr].types.push(tide.tide);
        //tidesByDate[dateStr].times.push(timeStr);
        //tidesByDate[dateStr].heights.push(tide.height);
        tidesByDate.push({ date: dateStr, event: timeStr+": "+tide.tide+" "+tide.height });
    });
    return tidesByDate;
}

// return height for a timestamp
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
        { date: "2023-08-04", event: "Event 1" },
        { date: "2023-08-13", event: "Event 2" },
        { date: "2023-08-18", event: "Event 3" },
        { date: "2023-08-18", event: "Event 4" },
        { date: "2023-08-19", event: tide_list[0].time_local },
        // Add more events here...
    ];

    // Get today's date in "YYYY-MM-DD"
    const todayDate = new Date().toISOString().split("T")[0];

    // Generate 6 weeks of calendar
    for (let week = 0; week < 6; week++) {
        const row = document.createElement("tr");

        for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
            const cell = document.createElement("td");
            const cellDate = currentDate.toISOString().split("T")[0]; // Format: "YYYY-MM-DD"
            cell.textContent = cellDate;
    
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

// Code starts here
// add the timestamp to the tide_list
for (var i=0; i < tide_list.length; i++) {
    tide_list[i].time_stamp = Date.parse(tide_list[i].time_local);
}

// Call function to generate the calendar
generateCalendar();
