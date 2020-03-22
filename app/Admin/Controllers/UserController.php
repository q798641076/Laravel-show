<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\User';

    /**
     * Make a grid builder.
     *决定列表页要展示哪些列，以及各个字段对应的名称，
     *Laravel-Admin 会通过读取数据库自动把所有的字段都列出来。
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('ID')->sortable();

        $grid->name('用户名');

        $grid->email('邮箱');

        $grid->email_verified_at('是否已验证邮箱')->display(function($value){
            return $value ? '是':'否';
        });

        $grid->created_at('注册时间');

        //不在页面显示‘新建’按钮，因为不需要在后台新建用户
        $grid->disableCreateButton();

        //不在页面显示编辑按钮
        $grid->disableActions();

        $grid->tools(function($tools){
            //禁用批量删除按钮
            $tools->batch(function ($batch){
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     * 用来展示用户详情页，通过调用 detail() 方法来决定要展示哪些字段，Laravel-Admin
     * 会通过读取数据库自动把所有的字段都列出来，由于我们的用户表没有太多多余的字段，
     * 在列表页就可以直接展示，因此可以把 detail() 方法也删掉。
     */
    // protected function detail($id)
    // {
    //     $show = new Show(User::findOrFail($id));

    //     $show->field('id', __('Id'));
    //     $show->field('name', __('Name'));
    //     $show->field('email', __('Email'));
    //     $show->field('email_verified_at', __('Email verified at'));
    //     $show->field('password', __('Password'));
    //     $show->field('remember_token', __('Remember token'));
    //     $show->field('created_at', __('Created at'));
    //     $show->field('updated_at', __('Updated at'));

    //     return $show;
    // }

    /**
     * Make a form builder.
     *用于编辑和创建用户，由于我们不会在管理后台去新增和编辑用户，所以可以把这个方法删除。
     * @return Form
     */
    // protected function form()
    // {
    //     $form = new Form(new User);

    //     $form->text('name', __('Name'));
    //     $form->email('email', __('Email'));
    //     $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
    //     $form->password('password', __('Password'));
    //     $form->text('remember_token', __('Remember token'));

    //     return $form;
    // }
}
