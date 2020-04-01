
  <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
      <div class="container">
      <a class="navbar-brand ml-3" href="{{route('/')}}">Laravel-Shop</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Dropdown
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">Action</a>
                <a class="dropdown-item" href="#">Another action</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">Something else here</a>
              </div>
            </li>
          </ul>
        <ul class="navbar-nav navbar-right">
         @guest
            <li class="nav-item"><a href="{{route('login')}}" class='nav-link'>登录</a></li>

            <li class="nav-item"><a href="{{route('register')}}" class='nav-link'>注册</a></li>
         @else
         <li class="nav-item"><a href="{{route('cart.index')}}" class="nav-link mt-1"><i class="fa fa-shopping-cart"></i></a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60"
                class="" width="30px" height="30px">
                {{Auth::user()->name}}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">个人信息</a>
              <a class="dropdown-item" href="{{route('user_addresses.index')}}">收货地址</a>
              <a class="dropdown-item" href="{{route('products.favoriteShow')}}">我的收藏</a>
              <a class="dropdown-item" href="{{route('orders.index')}}">我的订单</a>
              <div class="dropdown-divider"></div>

                {!! Form::open(['route'=>['logout'],'method'=>'post']) !!}
                 <button class=" btn dropdown-item" type="submit">退出登录</button>
                {!! Form::close() !!}

            </div>
          </li>
         @endguest
        </ul>
        </div>
      </nav>
