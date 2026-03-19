<style>
    .modern-user-form {
        --uf-bg: #ffffff;
        --uf-border: #e5e7eb;
        --uf-soft: #f8fafc;
        --uf-text: #111827;
        --uf-subtext: #6b7280;
        --uf-accent: #1d4ed8;
        margin-top: 8px;
    }

    .modern-user-form h2 {
        margin: 0 0 12px;
        font-size: 24px;
        font-weight: 700;
        color: var(--uf-text);
        letter-spacing: -0.02em;
    }

    .modern-user-form .form-box {
        border: 1px solid var(--uf-border);
        border-radius: 14px;
        background: var(--uf-bg);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        padding: 18px;
    }

    .modern-user-form .form-title {
        margin: 0 0 14px;
        color: var(--uf-text);
        font-size: 16px;
        font-weight: 700;
    }

    .modern-user-form .form-group {
        margin-bottom: 12px;
    }

    .modern-user-form .form-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--uf-subtext);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .modern-user-form .form-control {
        min-height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #111827;
        box-shadow: none;
    }

    .modern-user-form .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .modern-user-form .btn-green {
        border-radius: 8px;
        border: 1px solid #bbf7d0;
        background: #ecfdf5;
        color: #166534;
        font-weight: 700;
        padding: 9px 14px;
    }

    .modern-user-form .btn-green:hover {
        background: #dcfce7;
    }
</style>

<div class="modern-user-form">
    <h2>Add New <?php echo ucwords(rtrim(ltrim(ADMIN_BASE_URL, '/'), '/')); ?></h2>

    <div class="form-box">
        <form method="POST" id="registerFormData" enctype="multipart/form-data">
            <h3 class="form-title">Create User Account</h3>
            <div class="box-2">
                <div class="form-group">
                    <label>First Name*</label>
                    <input type="text" name="first_name" class='form-control inside-shadow' autofocus required>
                </div>
                <div class="form-group">
                    <label>Last Name*</label>
                    <input type="text" name="last_name" class='form-control inside-shadow' required>
                </div>
                <div class="form-group">
                    <label>Email*</label>
                    <input type="email" name='email' class='form-control inside-shadow' required>
                </div>
                <div class="form-group">
                    <label>Mobile No.</label>
                    <input type="number" onwheel="this.blur()" name='mobile' class='form-control inside-shadow'>
                </div>
                <div class="form-group">
                    <label>User Type*</label>
                    <select name='user_type' class='form-control inside-shadow' required>
                        <option value="">Select User Type</option>
                        <option value="administrator">Administrator</option>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="author">Author</option>
                        <option value="reporter">Reporter</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="hidden" name="website_id" value="<?php echo SITE_ID ?>">
                    <input type="hidden" name="website_user_id" value="<?php echo USER_ID ?>">
                    <input type="hidden" name="type" value="user">
                    <input type="hidden" name="action" value="add">
                    <input type="submit" id="registerBtn" class="btn btn-green" value="Add User">
                </div>
            </div>
        </form>
    </div>
</div>