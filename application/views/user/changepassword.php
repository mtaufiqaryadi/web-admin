<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <div class="row">
        <div class="col-lg-8">
            <?= $this->session->flashdata('flash'); ?>
            <form action="" method="post">
                <div class="form-group">
                    <label for="formGroupExampleInput">Current Password</label>
                    <input type="text" class="form-control" id="currentPassword" name="currentPassword">
                    <?= form_error('currentPassword', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">New Password</label>
                    <input type="text" class="form-control" id="newPassword1" name="newPassword1">
                    <?= form_error('newPassword1', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <div class="form-group">
                    <label for="formGroupExampleInput">Repeat Password</label>
                    <input type="text" class="form-control" id="newPassword2" name="newPassword2">
                    <?= form_error('newPassword2', '<small class="text-danger pl-3">', '</small>'); ?>
                </div>
                <button type="submit" class="btn btn-primary">Change Passowrd</button>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->