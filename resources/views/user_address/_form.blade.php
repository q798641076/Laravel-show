<user-addresses-create-and-edit inline-template>
    @if (isset($address->id))
    <form class="form-horizontal" role="form" method="POST" action="{{route('user_addresses.update',$address->id)}}">
        {{method_field('PUT')}}
    @else
     <form class="form-horizontal" role="form" method="POST" action="{{route('user_addresses.store')}}">
    @endif
      <!-- 引入 csrf token 字段 -->
    {{ csrf_field() }}
    <!-- 注意这里多了 @change -->
      <select-district :init-value="{{json_encode([
          old('province',$address->province),
          old('city',$address->city),
          old('district',$address->district)
          ])}}"
      @change="onDistrictChanged" inline-template>
        <div class="form-group row">
          <label class="col-form-label col-sm-2 text-md-right">省市区</label>
          <div class="col-sm-3">
            <select class="form-control" v-model="provinceId">
              <option value="">选择省</option>
              <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
            </select>
          </div>
          <div class="col-sm-3">
            <select class="form-control" v-model="cityId">
              <option value="">选择市</option>
              <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
            </select>
          </div>
          <div class="col-sm-3">
            <select class="form-control" v-model="districtId">
              <option value="">选择区</option>
              <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
            </select>
          </div>
        </div>
      </select-district>
      <!-- 插入了 3 个隐藏的字段 -->
      <!-- 通过 v-model 与 user-addresses-create-and-edit 组件里的值关联起来 -->
      <!-- 当组件中的值变化时，这里的值也会跟着变 -->
      <input type="hidden" name="province" v-model="province">
      <input type="hidden" name="city" v-model="city">
      <input type="hidden" name="district" v-model="district">
      <div class="form-group row">
        <label class="col-form-label text-md-right col-sm-2">详细地址</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-form-label text-md-right col-sm-2">邮编</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="zip" value="{{ old('zip', $address->zip) }}">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-form-label text-md-right col-sm-2">姓名</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
        </div>
      </div>
      <div class="form-group row">
        <label class="col-form-label text-md-right col-sm-2">电话</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
        </div>
      </div>
      <div class="form-group row text-center">
        <div class="col-12">
          <button type="submit" class="btn btn-primary">提交</button>
        </div>
      </div>
    </form>
  </user-addresses-create-and-edit>
