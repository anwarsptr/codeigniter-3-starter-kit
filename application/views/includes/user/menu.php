<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?= checkImg(get_session('foto'), 'user') ?>" class="img-circle" alt="User Image" style="max-height:45px;">
      </div>
      <div class="pull-left info">
        <p><?= get_session('name') ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- search form -->
    <form action="javascript:void(0);" method="get" class="sidebar-form hidden-xs" style="margin-top:0px;">
      <div class="input-group">
        <input type="text" name="q" class="form-control" id="sidebar-filter" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </form>
    <hr class="hidden-lg" style="margin:0px;">
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <?= buildMenuHTML() ?>
  </section>
</aside>
