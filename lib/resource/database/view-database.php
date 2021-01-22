<?php

  /** This page codes are dedicated for view tables only */

  // Daily page views

  mysqli_query($connection, "CREATE
    OR REPLACE VIEW ".TABLE_PREFIX."view_daily_page_view AS
    SELECT
      COUNT(analytics_id) AS page_view_total,
      analytics_site_id AS page_view_site_id,
      DATE(analytics_time) as page_view_time
    FROM
      ".TABLE_PREFIX."analytics
    WHERE
      DATE(analytics_time) = CURDATE()
    GROUP BY
    analytics_site_id
  ");