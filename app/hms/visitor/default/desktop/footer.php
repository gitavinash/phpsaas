
        
        <script type="text/javascript" src="<?php echo USER_JS ?>jquery-3.3.1.slim.min.js"></script>
        <script type="text/javascript" src="<?php echo USER_PLUGINS ?>daterangepicker/moment.min.js"></script>
        <script type="text/javascript" src="<?php echo USER_PLUGINS ?>daterangepicker/daterangepicker.js"></script>

        <script type="text/javascript">
            $('.datepicker').daterangepicker({
                "autoApply": true,
                "startDate": "<?php echo date('m/d/Y'); ?>",
                "minDate": "<?php echo date('m/d/Y'); ?>",
            });
        </script>

    </body>
</html>