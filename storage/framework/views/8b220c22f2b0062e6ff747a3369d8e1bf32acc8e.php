<nav class="navbar-default navbar-static-side " style="background: #2f4050; position: fixed;z-index: 2001;height: 100%;overflow-x: hidden; overflow-y: auto;" role="navigation">
    <div class="sidebar-collapse" id="xxx">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="index.html#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo e(Auth::guard('admin')->user()->name); ?></strong>
                             </span> <span class="text-muted text-xs block">Welcome !</span> </span> </a>

                </div>
                <div class="logo-element">
                    Admin CP
                </div>
            </li>
            <li class="<?php echo e(active_class(if_uri_pattern(['admin']),'active')); ?>"><a href="<?php echo e(url('admin')); ?>"><i class="fa fa-th-large"></i> 控制台</a></li>
            <?php $__currentLoopData = $sidebarMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                

                <?php if(!empty($item['child'])): ?>
                <li class="<?php echo e(active_class(if_uri_pattern(explode(',',$item['heightlight_url'])))); ?>">
                    <?php if(empty($item['child'])): ?>
                        <a href="<?php echo e(url($item['url'])); ?>#"><i class="<?php echo e($item['icon']); ?>"></i> <span class="nav-label"><?php echo e($item['name']); ?></span>  </a>
                        <?php else: ?>
                    <a href="<?php echo e(url($item['url'])); ?>#"><i class="<?php echo e($item['icon']); ?>"></i> <span class="nav-label"><?php echo e($item['name']); ?></span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level <?php echo e(active_class(if_uri_pattern([$item['heightlight_url']]),'collapse in',' collapse')); ?>">
                        <?php $__currentLoopData = $item['child']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_item): $__env->incrementLoopIndices(); $loop = $__env->getFirstLoop(); ?>
                            <?php if (\Entrust::can($_item['slug'])) : ?>
                            <li class="<?php echo e(active_class(if_uri_pattern([$_item['heightlight_url']]),'active')); ?>"><a href="<?php echo e(url($_item['url'])); ?>"><?php echo e($_item['name']); ?></a></li>
                            <?php endif; // Entrust::can ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endif; ?>
                
             <?php endforeach; $__env->popLoop(); $loop = $__env->getFirstLoop(); ?>
        </ul>
    </div>
</nav>
