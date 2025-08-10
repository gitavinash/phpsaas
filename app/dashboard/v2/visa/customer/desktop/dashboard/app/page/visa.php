<?php

require_once UTIL_FUNCTIONS . 'getInitials.php';

$applicationStatusQuery = "SELECT JSON_OBJECT(
  'counts', (
    SELECT JSON_OBJECT(
      'confirm', SUM(v.visa_application_status = 1),
      'in_process', SUM(v.visa_application_status = 4),
      'pending', SUM(v.visa_application_status = 0),
      'cancel', SUM(v.visa_application_status = 3)
    )
    FROM {$tableNamePrefix}application v
    LEFT JOIN " . TABLE_PREFIX . "order o 
      ON o.order_id = CAST(FROM_BASE64(v.visa_application_package_id) AS UNSIGNED)
         OR o.order_id = v.visa_application_package_id
    LEFT JOIN {$tableNamePrefix}package p 
      ON p.visa_package_id = CAST(FROM_BASE64(v.visa_application_package_id) AS UNSIGNED)
         OR p.visa_package_id = v.visa_application_package_id
    LEFT JOIN {$tableNamePrefix}country c 
      ON c.visa_country_id = p.visa_package_country_id
    WHERE v.visa_application_user_id = " . USER_ID . "
      AND v.visa_application_site_id = " . SITE_ID . "
  ),
  'recent', (
    SELECT JSON_ARRAYAGG(
      JSON_OBJECT(
        'visa_application_id', sub.visa_application_id,
        'visa_application_order_id', sub.visa_application_order_id,
        'visa_application_time', sub.visa_application_time,
        'visa_application_update', sub.visa_application_update,
        'visa_package_visa_type', sub.visa_package_visa_type,
        'visa_country_code', sub.visa_country_code,
        'visa_country_title', sub.visa_country_title,
        'visa_application_status',
          CASE sub.visa_application_status
            WHEN 1 THEN 'Completed'
            WHEN 4 THEN 'In Process'
            WHEN 0 THEN 'Pending'
            WHEN 3 THEN 'Cancel'
            ELSE 'Unknown'
          END
      )
    )
    FROM (
      SELECT 
        v.visa_application_id,
        v.visa_application_order_id,
        v.visa_application_time,
        v.visa_application_update,
        v.visa_application_status,
        p.visa_package_visa_type,
        c.visa_country_code,
        c.visa_country_title
      FROM {$tableNamePrefix}application v
      LEFT JOIN " . TABLE_PREFIX . "order o 
        ON o.order_id = CAST(FROM_BASE64(v.visa_application_package_id) AS UNSIGNED)
           OR o.order_id = v.visa_application_package_id
      LEFT JOIN {$tableNamePrefix}package p 
        ON p.visa_package_id = CAST(FROM_BASE64(v.visa_application_package_id) AS UNSIGNED)
           OR p.visa_package_id = v.visa_application_package_id
      LEFT JOIN {$tableNamePrefix}country c 
        ON c.visa_country_id = p.visa_package_country_id
      WHERE v.visa_application_user_id = " . USER_ID . "
        AND v.visa_application_site_id = " . SITE_ID . "
      ORDER BY v.visa_application_time DESC
      LIMIT 4
    ) AS sub
  )
) AS result";

if ($checkApplicationsInfo = mysqli_query($connection, $applicationStatusQuery)) {

    $row = mysqli_fetch_assoc($checkApplicationsInfo);
    $data = json_decode($row['result'], true);
    extract($data['counts']);
} else {
    echo (DEVELOPMENT_MODE === true)
        ? mysqli_error($connection)
        : 'Something went wrong kindly logout and retry';
    exit();
}

?>
<div class="max-w-7xl mx-auto px-8 py-12">
    <!-- Dashboard Content -->
    <div id="dashboard-content">
        <!-- Status Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Completed</p>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo (($confirm != '') ? $confirm : 0); ?>
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center">
                        <i class="ri-check-line text-xl text-green-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">In Progress</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            <?php echo (($in_process != '') ? $in_process : 0); ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center">
                        <i class="ri-time-line text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Pending</p>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo (($pending != '') ? $pending : 0); ?>
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center">
                        <i class="ri-hourglass-line text-xl text-orange-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Rejected</p>
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo (($cancel != '') ? $cancel : 0); ?>
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                        <i class="ri-close-line text-xl text-red-500"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Applications Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php

            if (isset($data['recent'])) {
                // Status color mapping
                $statusStyles = [
                    'completed'     => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'dot' => 'bg-green-500', 'label' => 'Confirmed', 'icon' => 'ri-check-line'],
                    'in process'  => ['bg' => 'bg-blue-100',  'text' => 'text-blue-800',  'dot' => 'bg-blue-500',  'label' => 'In Process', 'icon' => 'ri-time-line'],
                    'pending'     => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'dot' => 'bg-yellow-500', 'label' => 'Pending',    'icon' => 'ri-time-line'],
                    'cancel'      => ['bg' => 'bg-red-100',   'text' => 'text-red-800',   'dot' => 'bg-red-500',   'label' => 'Cancelled',  'icon' => 'ri-close-line'],
                    'unknown'     => ['bg' => 'bg-gray-100',  'text' => 'text-gray-800',  'dot' => 'bg-gray-500',  'label' => 'Unknown',    'icon' => 'ri-question-line'],
                ];

                foreach ($data['recent'] as $row) {
                    extract($row);
                    $statusKey = strtolower($visa_application_status);
                    $style = $statusStyles[$statusKey] ?? $statusStyles['unknown'];

                    echo "<a href='applications/view/?id=" . base64_encode($visa_application_id) . "' title='View application status in detail'>
        <div class='bg-white rounded-xl shadow-sm p-6 hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary group cursor-pointer relative overflow-hidden'>
            <div class='absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300'></div>
            <div class='relative'>
                <div class='flex items-start justify-between mb-6'>
                    <div class='flex items-center'>
                        <div class='w-12 h-9 bg-gray-50 p-1 border border-gray-200 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300'>
                            <img src='https://flagcdn.com/" . strtolower($visa_country_code) . ".svg' flag icon clean simple design&width=32&height=24' 
                                alt='{$visa_country_title}' class='w-full h-full object-contain'>
                        </div>
                        <div>
                            <h3 class='text-lg font-semibold text-gray-900 group-hover:text-primary transition-colors duration-300'>" . ucwords($visa_country_title) . "</h3>
                            <p class='text-sm text-gray-500'>" . ucwords($visa_package_visa_type) . " VISA</p>
                        </div>
                    </div>
                    <span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {$style['bg']} {$style['text']} group-hover:opacity-90 transition-colors duration-300'>
                        <div class='w-2 h-2 {$style['dot']} rounded-full mr-2'></div>
                        {$style['label']}
                    </span>
                </div>
                <div class='space-y-3 mb-6 p-4 bg-gray-50 rounded-lg group-hover:bg-gray-100/80 transition-colors duration-300'>
                    <div class='flex justify-between text-sm'>
                        <span class='text-gray-500'>Application ID:</span>
                        <span class='font-medium text-gray-900'>" . getInitials(SITE_NAME) . "-00" . (($visa_application_order_id != 0) ? $visa_application_order_id : 'REF' . $visa_application_id) . "</span>
                    </div>
                    <div class='flex justify-between text-sm'>
                        <span class='text-gray-500'>Applied:</span>
                        <span class='text-gray-700'>" . date('d, F Y h:i A', strtotime($visa_application_time)) . "</span>
                    </div>
                    <div class='flex justify-between text-sm'>
                        <span class='text-gray-500'>Last Updated:</span>
                        <span class='text-gray-700'>" . date('d, F Y h:i A', strtotime($visa_application_update)) . "</span>
                    </div>
                </div>
                <div class='flex items-center justify-between'>";
                    if ($statusKey === 'confirm') {
                        echo "<div class='flex items-center text-green-600'>
                    <div class='w-5 h-5 flex items-center justify-center mr-2 bg-green-100 rounded-full group-hover:scale-110 transition-transform duration-300'>
                        <i class='ri-check-line text-sm'></i>
                    </div>
                    <span class='text-sm font-medium'>VISA Approved</span>
                  </div>";
                    }

                    echo "      <button class='ml-auto flex items-center text-primary hover:text-blue-700 text-sm font-medium group-hover:translate-x-1 transition-transform duration-300'>
                        View Details
                        <i class='ri-arrow-right-line ml-1'></i>
                    </button>
                </div>
            </div>
        </div></a>";
                }
            } else {
                echo "
<div class='col-span-full'>
    <div class='bg-white rounded-xl shadow-sm p-8 border border-gray-100 text-center flex flex-col items-center justify-center min-h-[calc(100vh-200px)]'>
        <div class='w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3'>
            <i class='ri-file-list-2-line text-gray-400 text-2xl'></i>
        </div>
        <p class='text-gray-600 font-medium text-lg'>No applications found</p>
        <p class='text-gray-400 text-sm mt-1 mb-4'>Once you apply for a visa, your applications will appear here.</p>
        <a href='/services/' target='_blank'
            class='inline-flex items-center px-5 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition'>
            <span><i class='ri-file-add-line text-lg'></i> Start Visa Application</span>
            <i class='ri-external-link-line ml-2 text-xs opacity-80'></i>
        </a>
    </div>
</div>
";
            }

            ?>
        </div>

    </div>
</div>