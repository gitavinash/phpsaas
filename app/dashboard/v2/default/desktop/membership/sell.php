<h2 class="heading">Add new membership</h2>
<div class="main-box">
    <section>
        <form action="#" id="addNewMembershipForm" method="POST" enctype="multipart/form-data">
            <section class="form" style="padding-top:25px">
                <div class="step-open-box step_1_box">
                    <div class="form-group">
                        <input id="name" placeholder="Ajay gupta" name="name" type="text" required />
                        <label class="form-lable" id="Label">Customer name<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="mobile" name="mobile" placeholder="9999999999" type="number" required />
                        <label class="form-lable" id="Label">Mobile<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <label class="form-lable">Room type<sup>*</sup></label>
                        <select class="selectJS" style="float: left; width:100%" name="package" id="package">
                            <option value="deluxe" seleted>Deluxe</option>
                            <option value="luxury">Luxury</option>
                            <option value="semi_luxury">Semi Luxury</option>
                            <option value="super_deluxe">Super deluxe</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input id="price" placeholder="5000" name="price" type="number" required />
                        <label class="form-lable" id="Label">Price<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="dueAmount" value="0" name="dueAmount" type="number" required />
                        <label class="form-lable" id="Label">Any Due Amount?<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <input id="visit" value="5" name="visit" type="number" required />
                        <label class="form-lable" id="Label">Total visit<sup>*</sup></label>
                    </div>
                    <div class="form-group">
                        <textarea name="requirements" id="requirements" cols="30" rows="10"></textarea>
                        <label class="form-lable" id="Label">Any requirements</label>
                    </div>
                    <div class="box-1">
                        <div class="form-group">
                            <input type="button" class="btn btn-green" id="addMembership" value="Add new membership">
                        </div>
                    </div>
                </div>
            </section>
        </form>
    </section>
</div>