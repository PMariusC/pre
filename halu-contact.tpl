<div class="card-block">
    <h2 class="modhalucosh1">Contact</h2>
</div>
<hr class="separator">
<div>
    <form action="{$urls.base_url}halu-contact" method="post" {*{if $contact.allow_file_upload}enctype="multipart/form-data"{/if}*}>
        <section class="form-fields">
            <div class="form-group">
                <div class="col-md-6 modhalucos-forminput">
                    <input id="name" class="modhalucos-form-control" name="name" type="text" value="" placeholder="Name" required="required">
                </div>
                <div class="col-md-6 modhalucos-forminput">
                    <input id="surname" class="modhalucos-form-control" name="surname" type="text" value="" placeholder="Surname" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 modhalucos-forminput">
                    <input id="telefon" class="modhalucos-form-control" name="telefon" type="text" value="" placeholder="Phone" required="required">
                </div>
                <div class="col-md-6 modhalucos-forminput">
                    <input id="email" class="modhalucos-form-control" name="email" type="mail" value="" placeholder="E-mail" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 modhalucos-forminput">
                    <textarea id="message" class="modhalucos-form-controldos" name="message" placeholder="How can we help?" rows="3"></textarea>
                </div>
                <div class="col-md-12 modhalucos-forminput">
                    <div class="modhalucos-forminput">
                        {*<input type="checkbox" id="switchPresonal" required="required" class="modhalucos-forminput-check">
                        <label for="switchPresonal" class="form-check-label">I consent to the processing of personal data</label>*}
                        <label for="switchPresonal" class="modhalucos-form-check-label">
                            <input type="checkbox" id="switchPresonal" required="required" name="switchPresonal" class="modhalucos-forminput-check">
                            <span class="haluslider haluround"></span>
                        </label>
                         <label for="switchPresonal" class="form-check-label modhalucos-form-check-labeldos">I consent to the processing of personal data</label>
                    </div>

                    {$arrayprodid = []}
                    {$arrayprodqty = []}
                    {foreach from=$cart.products item=xproduct}
                        {$arrayprodid[] = $xproduct.id_product}
                        {$arrayprodqty[] = $xproduct.quantity}
                     {/foreach}       
                    <input type="hidden" name="id_products" value="{','|implode:$arrayprodid}" />
                    <input type="hidden" name="products_qty" value="{','|implode:$arrayprodqty}" />
                    <input class="btn modhalucosbtn-primary" type="submit" name="submitMessage" value="Submit"> 
                </div>
            </div>
        </section>     
    </form>
</div>