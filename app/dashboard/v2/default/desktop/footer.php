				</div>

				<footer>
				    <div class="credit">
				        <p><a href="<?php echo CREDIT_URL ?>" title="Developed by <?php echo CREDIT ?>" target="_blank">Powered by
				                <a href='https://aleya.in' target="_blank"><?php echo ucwords(SITE_APP) ?><sup
				                        class="trademark">TM</sup></a> | Made
				                with <i class="fi-rr-heart"></i> in
				                India.</a></p>
				    </div>
				</footer>
				<div class="shortcut">
				    <a href="https://support.blumox.com/?url=<?php echo urlencode(SITE_URL.ACTIVE_URL.'&domain='.SITE_DOMAIN) ?>"
				        target="_blank" title="Support"><i class="fi-rr-headset"></i></a>
				</div>
				</main>

				<script src="<?php echo JS_LIBRARY ?>jquery.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>editor/editor.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js' type='text/javascript'></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>chartjs/chart.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.min.js"></script>
				<?php
				
				if($site_access_type =='agent') { ?>
				<script>
const tempId = '<?php echo base64_encode(USER_ID) ?>';
(function() {
    window.sib = {
        equeue: [],
        client_key: "mqhe06o02h94s5loydr7defs"
    };
    /* OPTIONAL: email for identify request*/
    // window.sib.email_id = 'example@domain.com';
    window.sendinblue = {};
    for (var j = ['track', 'identify', 'trackLink', 'page'], i = 0; i < j.length; i++) {
        (function(k) {
            window.sendinblue[k] = function() {
                var arg = Array.prototype.slice.call(arguments);
                (window.sib[k] || function() {
                    var t = {};
                    t[k] = arg;
                    window.sib.equeue.push(t);
                })(arg[0], arg[1], arg[2]);
            };
        })(j[i]);
    }
    var n = document.createElement("script"),
        i = document.getElementsByTagName("script")[0];
    n.type = "text/javascript", n.id = "sendinblue-js",
        n.async = !0, n.src = "https://sibautomation.com/sa.js?key=" + window.sib.client_key,
        i.parentNode.insertBefore(n, i), window.sendinblue.page();
})();
				</script>
				<?php
				}

				?>
				<script src="<?php echo ADMIN_JS ?>app.min.js"></script>


				</body>

				</html>