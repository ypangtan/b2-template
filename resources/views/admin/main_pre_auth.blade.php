<?php echo view( 'admin/header', [ 'basic' => @$basic ] ); ?>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <?php echo view( $content ); ?>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset( 'admin/js/app.js' ) . Helper::assetVersion() }}"></script>
    
</body>

</html>