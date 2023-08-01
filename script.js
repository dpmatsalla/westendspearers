document.addEventListener('DOMContentLoaded', function () {
  const calendarContainer = document.getElementById('calendar');

  // Sample data - Replace this with your own event data
  const events = [
    { date: '2023-08-16', title: 'Event 1' },
    { date: '2023-08-22', title: 'Event 2' },
    { date: '2023-08-28', title: 'Event 3' },
  ];

  const currentDate = new Date();
  const firstDayToShow = currentDate - currentDate.getDay();
  if (currentDate.getDay() == 0) { firstDayToShow -= 7; }
  const lastDayToShow = firstDayToShow + 41;
  let dayOfWeek = firstDayToShow.getDay();  // Initialize dayOfWeek

  let calendarHTML = '<table>';
  calendarHTML += '<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>';

  let day = firstDayToShow;

  while (day <= lastDayToShow) {
    const formattedDate = formatDate(day);

    if (dayOfWeek === 0) {
      calendarHTML += '<tr>';
    }

    const event = events.find((event) => event.date === formattedDate);
    let cellContent = formattedDate;
    if (event) {
      cellContent += `<br><span class="event">${event.title}</span>`;
    }
    calendarHTML += `<td>${cellContent}</td>`;

    if (dayOfWeek === 6) {
      calendarHTML += '</tr>';
    }

    day++;
    dayOfWeek = (dayOfWeek + 1) % 7; // Update dayOfWeek for the next day
  }

  // Add empty cells to complete the last week if needed
  while (dayOfWeek !== 0) {
    calendarHTML += '<td></td>';
    dayOfWeek = (dayOfWeek + 1) % 7;
  }

  calendarHTML += '</tr>';
  calendarHTML += '</table>';

  calendarContainer.innerHTML = calendarHTML;
});

function formatDate(date) {
  const day = date.getDate().toString().padStart(2, '0');
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const year = date.getFullYear().toString().slice(-2);

  return `${day}/${month}/${year}`;
}
