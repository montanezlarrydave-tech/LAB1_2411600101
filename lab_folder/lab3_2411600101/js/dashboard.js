document.addEventListener('DOMContentLoaded', function () {

  const isloggedIn = localStorage.getItem('isloggedIn');
  if (localStorage.getItem('isLoggedIn') !== 'true') {
    window.location.href = 'index.html';
    return;
  }

  const username = localStorage.getItem('user') || 'User';

  updateGreeting(username);
  updateStatistics();
  populateActivityTable();
  setupLogout();

  const userNameSpan = document.getElementById('userName');
  if (userNameSpan) {
    userNameSpan.textContent = username;
  }
});

function updateGreeting(username) {
  const greetingElement = document.getElementById('greeting');
  if (!greetingElement) return;

  const hour = new Date().getHours();
  let timeOfDay = '';

  if (hour >= 5 && hour < 12) {
    timeOfDay = 'Good Morning';
  } else if (hour >= 12 && hour < 17) {
    timeOfDay = 'Good Afternoon';
  } else if (hour >= 17 && hour < 21) {
    timeOfDay = 'Good Evening';
  } else {
    timeOfDay = 'Good Night';
  }

  greetingElement.textContent = timeOfDay + ', ' + username + '!';
}

function updateStatistics() {
  const stats = [
    { title: 'Daily Steps', value: '8,542', color: 'text-primary', icon: '👟' },
    { title: 'Calories Burned', value: '420 kcal', color: 'text-success', icon: '🔥' },
    { title: 'Workouts', value: '3', color: 'text-info', icon: '💪' },
    { title: 'Avg Heart Rate', value: '72 bpm', color: 'text-warning', icon: '❤️' }
  ];

  stats.forEach(function (stat, index) {
    const titleElement = document.getElementById('stat' + (index + 1) + '-title');
    const valueElement = document.getElementById('stat' + (index + 1) + '-value');

    if (titleElement) {
      titleElement.textContent = stat.title;
    }
    if (valueElement) {
      valueElement.textContent = stat.value;
      valueElement.className = 'card-text fw-bold ' + stat.color;
    }
  });
}

function populateActivityTable() {
  const tableBody = document.getElementById('activityTableBody');
  if (!tableBody) return;

  const activities = [
    { date: '2026-08-16 07:30', activity: 'Morning Run - 5.2 km completed', status: 'success' },
    { date: '2026-08-15 18:45', activity: 'Strength Training - Upper Body', status: 'success' },
    { date: '2026-08-15 12:10', activity: 'Hydration Goal Reached (2.5L)', status: 'info' },
    { date: '2026-08-14 06:00', activity: 'Missed Yoga Session', status: 'danger' },
    { date: '2026-08-13 19:20', activity: 'HIIT Workout - 30 min', status: 'success' }
  ];

  tableBody.innerHTML = '';

  activities.forEach(function (activity) {
    const row = document.createElement('tr');
    let badgeClass = 'bg-secondary';

    if (activity.status === 'success') {
      badgeClass = 'bg-success';
    } else if (activity.status === 'warning') {
      badgeClass = 'bg-warning text-dark';
    } else if (activity.status === 'danger') {
      badgeClass = 'bg-danger';
    } else if (activity.status === 'info') {
      badgeClass = 'bg-info text-dark';
    }

    row.innerHTML =
      '<td>' + activity.date + '</td>' +
      '<td>' + activity.activity + '</td>' +
      '<td><span class="badge ' + badgeClass + '">' + activity.status + '</span></td>';

    tableBody.appendChild(row);
  });
}

function setupLogout() {
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutLink = document.getElementById('logoutLink');

  function performLogout(e) {
    if (e) e.preventDefault();
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('user');
    window.location.href = 'index.html';
  }

  if (logoutBtn) {
    logoutBtn.addEventListener('click', performLogout);
  }
  if (logoutLink) {
    logoutLink.addEventListener('click', performLogout);
  }
}
