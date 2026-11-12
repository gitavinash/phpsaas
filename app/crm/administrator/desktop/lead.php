<div class="crm-lead-ui">
<h2>Add new <?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?></h2>

<div class="form-box">
    <form method="POST" id="formData" enctype="multipart/form-data">
        <div class="left-box">
            <div class="form-group">
                <label for="">Full Name</label>
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Full Name"
                    autofocus required>
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="">Mobile No.</label>
                <input type="text" id="title" class="form-control inside-shadow" name="title" placeholder="Mobile No"
                    required>
            </div>
            <div class="form-group">
                <label for="">Date of Birth</label>
                <input type="text" id="title" class="form-control inside-shadow" name="title"
                    placeholder="Date of Birth">
            </div>
            <div class="form-group">
                <label for="">Time of Birth</label>
                <div class="box-2">
                    <div class="form-group">
                        <label>Hours ( 24 Hours Format )</label>
                        <select class="form-control inside-shadow" name="hourOfBirth" id="gender">
                            <option value="" disabled="" selected="">HH</option>
                            <option value="00">00 (12 midnight)</option>
                            <option value="01">01 (am)</option>
                            <option value="02">02 (am)</option>
                            <option value="03">03 (am)</option>
                            <option value="04">04 (am)</option>
                            <option value="05">05 (am)</option>
                            <option value="06">06 (am)</option>
                            <option value="07">07 (am)</option>
                            <option value="08">08 (am)</option>
                            <option value="09">09 (am)</option>
                            <option value="10">10 (am)</option>
                            <option value="11">11 (am)</option>
                            <option value="12">12 (noon)</option>
                            <option value="13">13 (1 pm)</option>
                            <option value="14">14 (2 pm)</option>
                            <option value="15">15 (3 pm)</option>
                            <option value="16">16 (4 pm)</option>
                            <option value="17">17 (5 pm)</option>
                            <option value="18">18 (6 pm)</option>
                            <option value="19">19 (7 pm)</option>
                            <option value="20">20 (8 pm)</option>
                            <option value="21">21 (9 pm)</option>
                            <option value="22">22 (10 pm)</option>
                            <option value="23">23 (11 pm)</option>
                        </select>
                    </div>
                </div>
                <div class="box-2">
                    <div class="form-group">
                        <label>Minutes</label>
                        <select class="form-control inside-shadow" name="minuteOfBirth">
                            <option value="">MM</option>
                            <option value="00">00</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                            <option value="32">32</option>
                            <option value="33">33</option>
                            <option value="34">34</option>
                            <option value="35">35</option>
                            <option value="36">36</option>
                            <option value="37">37</option>
                            <option value="38">38</option>
                            <option value="39">39</option>
                            <option value="40">40</option>
                            <option value="41">41</option>
                            <option value="42">42</option>
                            <option value="43">43</option>
                            <option value="44">44</option>
                            <option value="45">45</option>
                            <option value="46">46</option>
                            <option value="47">47</option>
                            <option value="48">48</option>
                            <option value="49">49</option>
                            <option value="50">50</option>
                            <option value="51">51</option>
                            <option value="52">52</option>
                            <option value="53">53</option>
                            <option value="54">54</option>
                            <option value="55">55</option>
                            <option value="56">56</option>
                            <option value="57">57</option>
                            <option value="58">58</option>
                            <option value="59">59</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <div class="right-box">
            <div class="action-box inside-shadow">
                <input type="button" id="publishPost" class="btn btn-green btn-block" value="Publish">
                <!-- <input type="submit" id="draftPost" class="btn btn-yellow btn-block" value="Save Draft"> -->
                <input type="hidden" name="type" value="<?php echo rtrim(ltrim(ADMIN_BASE_URL, '/'), '/'); ?>">
                <input type="hidden" name="action" value="publish">
            </div>
        </div>
    </form>
</div>
</div>