<?php
require_once UTIL_FUNCTIONS;

/**
 * Helper to safely get a value from the profile array.
 */
function profile_val(array $arr, string $key, $fallback = '')
{
    return isset($arr[$key]) ? $arr[$key] : $fallback;
}

$profile = [];
if (defined('SETTING_COMPANY_PROFILE')) {
    $decoded = json_decode(SETTING_COMPANY_PROFILE, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $profile = $decoded;
    }
}

?>

<h2>Manage Company Profile</h2>

<div class="form-box">
    <div class="box-2">
        <form id="companyProfileData">
            <!-- Company & Ownership -->
            <div class="form-group">
                <label for="company_name">Company Name <span style="color:#d00">*</span></label>
                <input type="text" id="company_name" name="company_name" class="form-control inside-shadow"
                    placeholder="e.g., Acme Pvt. Ltd."
                    value="<?php echo htmlspecialchars(profile_val($profile, 'company_name'), ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <label for="owner_name">Owner / Proprietor Name <span style="color:#d00">*</span></label>
                <input type="text" id="owner_name" name="owner_name" class="form-control inside-shadow"
                    placeholder="e.g., John Doe"
                    value="<?php echo htmlspecialchars(profile_val($profile, 'owner_name'), ENT_QUOTES); ?>" required>
            </div>

            <!-- Domain -->
            <div class="form-group">
                <label for="domain">Company Domain <span style="color:#d00">*</span></label>
                <input type="url" id="domain" name="domain" class="form-control inside-shadow"
                    value="<?php echo SITE_DOMAIN ?>" readonly>
            </div>

            <!-- Intro -->
            <div class="form-group">
                <label for="intro">Short Intro (under 300 characters) <span style="color:#d00">*</span></label>
                <textarea id="intro" name="intro" class="form-control inside-shadow"
                    placeholder="Briefly describe your company..." maxlength="300" rows="3"
                    required><?php echo htmlspecialchars(profile_val($profile, 'intro'), ENT_QUOTES); ?></textarea>
                <small id="introCounter" class="muted">0 / 300</small>
            </div>

            <div class="form-group">
                <label for="account_email">Account Email</label>
                <input type="email" id="account_email" class="form-control inside-shadow"
                    value="<?php echo htmlspecialchars(profile_val($profile, 'email'), ENT_QUOTES); ?>"
                    placeholder="Email">
            </div>

            <!-- Notification channels -->
            <div class="form-group">
                <label for="email_notify">Notification Email</label>
                <input type="email" id="email_notify" name="email_notify" class="form-control inside-shadow"
                    placeholder="alerts@example.com"
                    value="<?php echo htmlspecialchars(profile_val($profile, 'email_notify'), ENT_QUOTES); ?>">
            </div>

            <div class="form-group">
                <label for="mobile">Primary Mobile <span style="color:#d00">*</span></label>
                <input type="tel" id="mobile" name="mobile" class="form-control inside-shadow"
                    placeholder="e.g., +91 98765 43210"
                    value="<?php echo htmlspecialchars(profile_val($profile, 'mobile'), ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile_notify">Notification Mobile</label>
                <input type="tel" id="mobile_notify" name="mobile_notify" class="form-control inside-shadow"
                    placeholder="e.g., +91 98765 43210"
                    value="<?php echo htmlspecialchars(profile_val($profile, 'mobile_notify'), ENT_QUOTES); ?>">
            </div>
        </form>
    </div>
    <div class="box-2">


        <div class="form-group">
            <label for="whatsapp">Primary WhatsApp <span style="color:#d00">*</span></label>
            <input type="tel" id="whatsapp" name="whatsapp" class="form-control inside-shadow"
                placeholder="e.g., +91 98765 43210"
                value="<?php echo htmlspecialchars(profile_val($profile, 'whatsapp'), ENT_QUOTES); ?>" required>
        </div>

        <div class="form-group">
            <label for="whatsapp_notify">Notification WhatsApp</label>
            <input type="tel" id="whatsapp_notify" name="whatsapp_notify" class="form-control inside-shadow"
                placeholder="e.g., +91 98765 43210"
                value="<?php echo htmlspecialchars(profile_val($profile, 'whatsapp_notify'), ENT_QUOTES); ?>">
        </div>

        <!-- Address -->
        <div class="form-group">
            <label for="address">Registered Address <span style="color:#d00">*</span></label>
            <textarea id="address" name="address" class="form-control inside-shadow"
                placeholder="Street, City, State, PIN / ZIP" rows="3"
                required><?php echo htmlspecialchars(profile_val($profile, 'address'), ENT_QUOTES); ?></textarea>
        </div>

        <!-- Socials -->
        <div class="form-group">
            <label for="facebook">Facebook</label>
            <input type="url" id="facebook" name="facebook" class="form-control inside-shadow"
                placeholder="https://facebook.com/yourpage"
                value="<?php echo htmlspecialchars(profile_val($profile, 'facebook'), ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label for="x">X (formerly Twitter)</label>
            <input type="url" id="x" name="x" class="form-control inside-shadow" placeholder="https://x.com/yourhandle"
                value="<?php echo htmlspecialchars(profile_val($profile, 'x'), ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label for="linkedin">LinkedIn</label>
            <input type="url" id="linkedin" name="linkedin" class="form-control inside-shadow"
                placeholder="https://linkedin.com/company/yourcompany"
                value="<?php echo htmlspecialchars(profile_val($profile, 'linkedin'), ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label for="instagram">Instagram</label>
            <input type="url" id="instagram" name="instagram" class="form-control inside-shadow"
                placeholder="https://instagram.com/yourhandle"
                value="<?php echo htmlspecialchars(profile_val($profile, 'instagram'), ENT_QUOTES); ?>">
        </div>

        <div class="form-group">
            <label for="youtube">YouTube</label>
            <input type="url" id="youtube" name="youtube" class="form-control inside-shadow"
                placeholder="https://youtube.com/@yourchannel"
                value="<?php echo htmlspecialchars(profile_val($profile, 'youtube'), ENT_QUOTES); ?>">
        </div>

        <!-- Hidden meta -->
        <div class="form-group">
            <input type="hidden" name="type" value="setting">
            <input type="hidden" name="action" value="companyProfile">
            <input type="submit" id="updateCompanyProfile" class="btn btn-green" value="Update Company Profile">
        </div>
    </div>

</div>

<script>
// Intro character counter (300 max)
(function() {
    var intro = document.getElementById('intro');
    var counter = document.getElementById('introCounter');
    if (!intro || !counter) return;

    function updateCount() {
        var len = intro.value.length || 0;
        counter.textContent = len + ' / 300';
    }
    intro.addEventListener('input', updateCount);
    updateCount();
})();
</script>