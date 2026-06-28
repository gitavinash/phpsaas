<?php

require_once 'header.php';

?>

<header class="events-header">
  <div class="container">
    <h1>Community Events</h1>
    <p>
      Explore upcoming programs and revisit our completed outreach efforts. Use
      the filters to view upcoming, past, or both event types.
    </p>
  </div>
</header>

<?php

$eventData = getEvents($connection, '', false, 100, 'event_start_date', 'DESC');
$defaultEventImage = 'assets/images/gallery/events/1000071350.jpg';
$todayTimestamp = strtotime(date('Y-m-d 00:00:00'));

?>

<section class="section-padding" style="padding-top: 0">
  <div class="container">
    <div class="events-toolbar" role="tablist" aria-label="Event filters">
      <button class="events-filter-btn is-active" type="button" data-filter="all">Both Events</button>
      <button class="events-filter-btn" type="button" data-filter="upcoming">Upcoming Events</button>
      <button class="events-filter-btn" type="button" data-filter="past">Past Events</button>
    </div>

    <div class="events-grid" id="events-grid">
      <?php

      if (!empty($eventData)) {
        foreach ($eventData as $event) {
          $title = isset($event['title']) ? trim($event['title']) : '';
          $description = isset($event['description']) ? trim($event['description']) : '';
          $location = isset($event['location']) ? trim($event['location']) : '';
          $eventDate = isset($event['event_date']) ? trim($event['event_date']) : '';
          $eventImage = isset($event['image']) ? trim($event['image']) : '';
          $eventLink = isset($event['link_url']) ? trim($event['link_url']) : '#';

          $eventTimestamp = ($eventDate !== '') ? strtotime($eventDate) : false;
          $isPast = ($eventTimestamp !== false) ? ($eventTimestamp < $todayTimestamp) : false;
          $eventType = $isPast ? 'past' : 'upcoming';
          $eventChipClass = $isPast ? 'past' : 'upcoming';
          $eventChipLabel = $isPast ? 'Past' : 'Upcoming';

          $eventDateLabel = ($eventTimestamp !== false) ? date('d M, Y', $eventTimestamp) : 'Date TBA';
          $eventTimeLabel = ($eventTimestamp !== false) ? date('h:i A', $eventTimestamp) : 'Time TBA';

          if ($eventTimestamp !== false && date('H:i:s', $eventTimestamp) === '00:00:00') {
            $eventTimeLabel = 'All Day';
          }

          $imageSource = ($eventImage !== '') ? $eventImage : $defaultEventImage;
          $safeTitle = htmlspecialchars(($title !== '') ? $title : 'Event', ENT_QUOTES, 'UTF-8');
          $safeDescription = htmlspecialchars(($description !== '') ? $description : 'Details will be available soon.', ENT_QUOTES, 'UTF-8');
          $safeLocation = htmlspecialchars(($location !== '') ? $location : 'Location to be announced', ENT_QUOTES, 'UTF-8');
          $safeDate = htmlspecialchars($eventDateLabel, ENT_QUOTES, 'UTF-8');
          $safeTime = htmlspecialchars($eventTimeLabel, ENT_QUOTES, 'UTF-8');
          $safeImage = htmlspecialchars($imageSource, ENT_QUOTES, 'UTF-8');
          $safeLink = htmlspecialchars($eventLink, ENT_QUOTES, 'UTF-8');
      ?>
          <article class="event-item is-<?php echo $eventType; ?>" data-type="<?php echo $eventType; ?>">
            <div class="event-image-wrap">
              <img src="<?php echo $safeImage; ?>" alt="<?php echo $safeTitle; ?>" class="event-image" />
              <span class="event-chip <?php echo $eventChipClass; ?>"><?php echo $eventChipLabel; ?></span>
            </div>
            <div class="event-content">
              <h3><?php echo $safeTitle; ?></h3>
              <div class="event-meta">
                <span><i class="fas fa-calendar-alt"></i> <?php echo $safeDate; ?></span>
                <span><i class="fas fa-clock"></i> <?php echo $safeTime; ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?php echo $safeLocation; ?></span>
              </div>
              <p><?php echo $safeDescription; ?></p>
              <div class="event-actions">
                <a href="<?php echo $safeLink; ?>" class="btn btn-primary"><?php echo $isPast ? 'View Event' : 'Join Event'; ?></a>
              </div>
            </div>
          </article>
      <?php
        }
      }

      ?>
    </div>

    <p class="events-empty" id="events-empty">No events available for the selected filter.</p>
  </div>
</section>

<script>
  (function() {
    var filterButtons = document.querySelectorAll('.events-filter-btn');
    var events = document.querySelectorAll('#events-grid .event-item');
    var emptyState = document.getElementById('events-empty');

    function applyFilter(filterType) {
      var visibleCount = 0;

      events.forEach(function(eventItem) {
        var eventType = eventItem.getAttribute('data-type');
        var showItem = filterType === 'all' || eventType === filterType;

        eventItem.style.display = showItem ? 'flex' : 'none';
        if (showItem) {
          visibleCount += 1;
        }
      });

      emptyState.style.display = visibleCount ? 'none' : 'block';
    }

    filterButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        filterButtons.forEach(function(btn) {
          btn.classList.remove('is-active');
        });

        button.classList.add('is-active');
        applyFilter(button.getAttribute('data-filter'));
      });
    });

    applyFilter('all');
  })();
</script>

<div id="footer-placeholder"></div>
<?php

require_once 'footer.php';

?>