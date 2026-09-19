document.addEventListener('DOMContentLoaded', () => {
  // 1. Digital Clock & Date Display
  function updateLiveClock() {
    const clockElement = document.getElementById('digitalClock');
    const dateElement = document.getElementById('dateStamp');
    const now = new Date();

    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;
    const formattedHours = String(hours).padStart(2, '0');

    if (clockElement) {
      clockElement.textContent = `${formattedHours}:${minutes}:${seconds} ${ampm}`;
    }

    if (dateElement) {
      const options = { weekday: 'long', month: 'short', day: '2-digit', year: 'numeric' };
      dateElement.textContent = now.toLocaleDateString('en-US', options).toUpperCase();
    }
  }
  setInterval(updateLiveClock, 1000);
  updateLiveClock();

  // 2. Motivational Quote Rotator
  const quotes = [
    '"Hard work beats talent."',
    '"Believe you can."',
    '"Discipline is the bridge between goals and accomplishment."',
    '"Officer Like Qualities are built, not born."',
    '"Success is where preparation and opportunity meet."'
  ];
  let quoteIndex = 0;
  const quoteRotator = document.getElementById('quoteRotator');
  setInterval(() => {
    quoteIndex = (quoteIndex + 1) % quotes.length;
    if (quoteRotator) {
      quoteRotator.style.opacity = '0';
      setTimeout(() => {
        quoteRotator.textContent = quotes[quoteIndex];
        quoteRotator.style.opacity = '1';
      }, 300);
    }
  }, 6000);

  // 3. Navigation View Switcher
  const navButtons = document.querySelectorAll('.sidebar-menu .nav-item');
  const viewPanels = document.querySelectorAll('.view-panel');

  function switchView(targetViewId) {
    viewPanels.forEach(panel => panel.classList.remove('active-view'));
    navButtons.forEach(btn => btn.classList.remove('active'));

    const activePanel = document.getElementById(`view-${targetViewId}`);
    if (activePanel) {
      activePanel.classList.add('active-view');
    }

    const matchedNavBtn = document.querySelector(`.sidebar-menu [data-view="${targetViewId}"]`);
    if (matchedNavBtn) {
      matchedNavBtn.classList.add('active');
    }

    // Close sidebar drawer on mobile after clicking
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768 && sidebar) {
      sidebar.classList.remove('open');
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  navButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetView = btn.getAttribute('data-view');
      switchView(targetView);
    });
  });

  // Dedicated direct links
  const issbGuideTrigger = document.getElementById('openIssbGuide');
  if (issbGuideTrigger) {
    issbGuideTrigger.addEventListener('click', () => switchView('issb-activities'));
  }

  const userProfileTrigger = document.getElementById('userProfileBtn');
  if (userProfileTrigger) {
    userProfileTrigger.addEventListener('click', () => switchView('profile'));
  }

  document.querySelectorAll('[data-view-target]').forEach(elem => {
    elem.addEventListener('click', () => {
      const target = elem.getAttribute('data-view-target');
      switchView(target);
    });
  });

  // 4. Interactive Quiz Option Checker
  const questionCards = document.querySelectorAll('.question-card');
  questionCards.forEach(card => {
    const correctAnswer = card.getAttribute('data-correct');
    const optionLabels = card.querySelectorAll('.option-label');

    optionLabels.forEach(label => {
      label.addEventListener('click', () => {
        const chosenOpt = label.getAttribute('data-opt');

        // Reset styling for current question
        optionLabels.forEach(l => l.classList.remove('correct', 'incorrect'));

        if (chosenOpt === correctAnswer) {
          label.classList.add('correct');
        } else {
          label.classList.add('incorrect');
          // Highlight the actual correct option
          const correctLabel = card.querySelector(`[data-opt="${correctAnswer}"]`);
          if (correctLabel) correctLabel.classList.add('correct');
        }
      });
    });

    // Toggle Answer Explanation
    const explanationBtn = card.querySelector('.toggle-explanation-btn');
    const explanationBox = card.querySelector('.explanation-box');
    if (explanationBtn && explanationBox) {
      explanationBtn.addEventListener('click', () => {
        explanationBox.classList.toggle('active');
        explanationBtn.innerHTML = explanationBox.classList.contains('active')
          ? '<i class="fa-solid fa-eye-slash"></i> HIDE EXPLANATION'
          : '<i class="fa-solid fa-lightbulb"></i> SHOW EXPLANATION';
      });
    }
  });

  // 5. Mobile Drawer Toggle
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // 6. Dismissible Notification Banner
  const closeBanner = document.getElementById('closeBanner');
  const installBanner = document.getElementById('installBanner');
  if (closeBanner && installBanner) {
    closeBanner.addEventListener('click', () => {
      installBanner.style.display = 'none';
    });
  }

  // 7. Profile Target Service Switcher
  const serviceButtons = document.querySelectorAll('.btn-service');
  serviceButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      serviceButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });
});