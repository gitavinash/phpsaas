
<tr>
                                    <td class="box-3"><strong>First name<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="first_name"
                                            id="firstName" value="<?php echo $enquiry_first_name ?>" required></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Middle name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="middle_name"
                                            id="middleName" value="<?php echo $enquiry_middle_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Last name</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="last_name"
                                            id="lastName" value="<?php echo $enquiry_last_name ?>"></td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Email</strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="email" id="email"
                                            value="<?php echo $enquiry_email ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Mobile<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="mobile" id="mobile"
                                            value="<?php echo $enquiry_mobile ?>" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Tour package<sup>*</sup></strong></td>
                                    <td class="box-7">
                                        <select class="selectJS form-control" name="package" id="select">
                                            <option value="">Select destination</option>
                                            <option value="Andaman and Nicobar Islands"
                                                <?php echo (($enquiry_subject == 'Andaman and Nicobar Islands') ? 'selected' : '') ?>>
                                                Andaman and Nicobar Islands
                                            </option>
                                            <option value="Andhra Pradesh"
                                                <?php echo (($enquiry_subject == 'Andhra Pradesh') ? 'selected' : '') ?>>
                                                Andhra Pradesh
                                            </option>
                                            <option value="Arunachal Pradesh"
                                                <?php echo (($enquiry_subject == 'Arunachal Pradesh') ? 'selected' : '') ?>>
                                                Arunachal Pradesh
                                            </option>
                                            <option value="Assam"
                                                <?php echo (($enquiry_subject == 'Assam') ? 'selected' : '') ?>>Assam
                                            </option>
                                            <option value="Bihar"
                                                <?php echo (($enquiry_subject == 'Bihar') ? 'selected' : '') ?>>Bihar
                                            </option>
                                            <option value="Chandigarh"
                                                <?php echo (($enquiry_subject == 'Chandigarh') ? 'selected' : '') ?>>
                                                Chandigarh
                                            </option>
                                            <option value="Chhattisgarh"
                                                <?php echo (($enquiry_subject == 'Chhattisgarh') ? 'selected' : '') ?>>
                                                Chhattisgarh
                                            </option>
                                            <option value="Dadra and Nagar Haveli and Daman & Diu"
                                                <?php echo (($enquiry_subject == 'Dadra and Nagar Haveli and Daman & Diu') ? 'selected' : '') ?>>
                                                Dadra and Nagar Haveli and Daman & Diu
                                            </option>
                                            <option value="Delhi"
                                                <?php echo (($enquiry_subject == 'Delhi') ? 'selected' : '') ?>>
                                                Delhi
                                            </option>
                                            <option value="Goa"
                                                <?php echo (($enquiry_subject == 'Goa') ? 'selected' : '') ?>>Goa
                                            </option>
                                            <option value="Gujarat"
                                                <?php echo (($enquiry_subject == 'Gujarat') ? 'selected' : '') ?>>
                                                Gujarat
                                            </option>
                                            <option value="Haryana"
                                                <?php echo (($enquiry_subject == 'Haryana') ? 'selected' : '') ?>>
                                                Haryana
                                            </option>
                                            <option value="Himachal Pradesh"
                                                <?php echo (($enquiry_subject == 'Himachal Pradesh') ? 'selected' : '') ?>>
                                                Himachal Pradesh
                                            </option>
                                            <option value="Jammu & Kashmir"
                                                <?php echo (($enquiry_subject == 'Jammu & Kashmir') ? 'selected' : '') ?>>
                                                Jammu & Kashmir
                                            </option>
                                            <option value="Jharkhand"
                                                <?php echo (($enquiry_subject == 'Jharkhand') ? 'selected' : '') ?>>
                                                Jharkhand
                                            </option>
                                            <option value="Karnataka"
                                                <?php echo (($enquiry_subject == 'Karnataka') ? 'selected' : '') ?>>
                                                Karnataka
                                            </option>
                                            <option value="Kerala"
                                                <?php echo (($enquiry_subject == 'Kerala') ? 'selected' : '') ?>>Kerala
                                            </option>
                                            <option value="Ladakh"
                                                <?php echo (($enquiry_subject == 'Ladakh') ? 'selected' : '') ?>>Ladakh
                                            </option>
                                            <option value="Lakshadweep"
                                                <?php echo (($enquiry_subject == 'Lakshadweep') ? 'selected' : '') ?>>
                                                Lakshadweep
                                            </option>
                                            <option value="Madhya Pradesh"
                                                <?php echo (($enquiry_subject == 'Madhya Pradesh') ? 'selected' : '') ?>>
                                                Madhya Pradesh
                                            </option>
                                            <option value="Maharashtra"
                                                <?php echo (($enquiry_subject == 'Maharashtra') ? 'selected' : '') ?>>
                                                Maharashtra
                                            </option>
                                            <option value="Manipur"
                                                <?php echo (($enquiry_subject == 'Manipur') ? 'selected' : '') ?>>
                                                Manipur
                                            </option>
                                            <option value="Meghalaya"
                                                <?php echo (($enquiry_subject == 'Meghalaya') ? 'selected' : '') ?>>
                                                Meghalaya
                                            </option>
                                            <option value="Mizoram"
                                                <?php echo (($enquiry_subject == 'Mizoram') ? 'selected' : '') ?>>
                                                Mizoram
                                            </option>
                                            <option value="Nagaland"
                                                <?php echo (($enquiry_subject == 'Nagaland') ? 'selected' : '') ?>>
                                                Nagaland
                                            </option>
                                            <option value="Odisha"
                                                <?php echo (($enquiry_subject == 'Odisha') ? 'selected' : '') ?>>Odisha
                                            </option>
                                            <option value="Puducherry"
                                                <?php echo (($enquiry_subject == 'Puducherry') ? 'selected' : '') ?>>
                                                Puducherry
                                            </option>
                                            <option value="Punjab"
                                                <?php echo (($enquiry_subject == 'Punjab') ? 'selected' : '') ?>>Punjab
                                            </option>
                                            <option value="Rajasthan"
                                                <?php echo (($enquiry_subject == 'Rajasthan') ? 'selected' : '') ?>>
                                                Rajasthan
                                            </option>
                                            <option value="Sikkim"
                                                <?php echo (($enquiry_subject == 'Sikkim') ? 'selected' : '') ?>>Sikkim
                                            </option>
                                            <option value="Tamil Nadu"
                                                <?php echo (($enquiry_subject == 'Tamil Nadu') ? 'selected' : '') ?>>
                                                Tamil
                                                Nadu
                                            </option>
                                            <option value="Telangana"
                                                <?php echo (($enquiry_subject == 'Telangana') ? 'selected' : '') ?>>
                                                Telangana
                                            </option>
                                            <option value="Tripura"
                                                <?php echo (($enquiry_subject == 'Tripura') ? 'selected' : '') ?>>
                                                Tripura
                                            </option>
                                            <option value="Uttarakhand"
                                                <?php echo (($enquiry_subject == 'Uttarakhand') ? 'selected' : '') ?>>
                                                Uttarakhand</option>
                                            <option value="Uttar Pradesh"
                                                <?php echo (($enquiry_subject == 'Uttar Pradesh') ? 'selected' : '') ?>>
                                                Uttar Pradesh
                                            </option>
                                            <option value="West Bengal"
                                                <?php echo (($enquiry_subject == 'West Bengal') ? 'selected' : '') ?>>
                                                West
                                                Bengal
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Date of travel<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="datepickerForFutureDate"
                                            name="date_of_travel" id="date_of_travel"
                                            value="<?php echo date('d-m-Y', strtotime($date_of_travel)) ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Travel duration<span class="secondLine">( in Nights
                                                )</span><sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_days"
                                            id="number_of_days" placeholder="3" value="<?php echo $number_of_days ?>"
                                            required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of adults<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_adults"
                                            id="number_of_adults" placeholder="0"
                                            value="<?php echo $number_of_adults ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of children<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_childrens"
                                            id="number_of_childrens" placeholder="0"
                                            value="<?php echo $number_of_childrens ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Number of infants<span class="secondLine">( Below 5
                                                years)</span><sup>*</sup></strong>
                                    </td>
                                    <td class="box-7"><input type="text" class="form-control" name="number_of_infants"
                                            id="number_of_infants" placeholder="0"
                                            value="<?php echo $number_of_infants ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Current location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="current_location"
                                            id="current_location" value="<?php echo $current_location ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Pickup location<sup>*</sup></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="pickup_location"
                                            id="pickup_location" value="<?php echo $pickup_location ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Drop location<span class="secondLine">( ignore if same
                                                )</span></strong></td>
                                    <td class="box-7"><input type="text" class="form-control" name="drop_location"
                                            id="drop_location" value="<?php echo $drop_location ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3"><strong>Any requirements</strong></td>
                                    <td class="box-7">
                                        <textarea name="requirements" class="form-control"
                                            id="requirements"><?php echo (($requirements == '') ? $enquiry_message : $requirements) ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="box-3">
                                    </td>
                                    <td class="box-7">
                                        <input type="hidden" name="leadId" value="<?php echo $enquiry_id ?>">
                                        <input type="submit" id="updateEnquiry" class="btn btn-green"
                                            value="Update lead">
                                    </td>
                                </tr>