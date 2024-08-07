@php
use App\Models\Module;
$module = Module::with('permission')->where('status', Constant::ACTIVE)->get();
@endphp



<div class="row">
    <div class="col mb-3">
        <label for="nameBackdrop" class="form-label">{{__('Name')}}</label>
        {{ Form::text('name',$data->name ?? '' ,['class' =>'form-control', 'placeholder' =>'Enter Role Name',  'required' , 'maxlength'=>'30']) }}
    </div>
    {!! Form::hidden('id', $data->id ?? '') !!}
</div>


<div class="row g-2">
    <div class="form-check mt-3"><input class="form-check-input checkbox" id="selectall" type="checkbox" value="Group A 1" />
        <label class="form-check-label" for="cbx-select-all">{{__('Select ALL Permission')}} </label>
    </div>

    @if(isset($module) && count($module) > 0 )
    @foreach($module as $key => $item )


    <label class="form-check-label"> <input class="form-check-input checkbox module selectedId"  data-manager_id="manager_{{$item->id}}" id="manager_{{$item->id}}" onchange="checkBoxModule('manager_{{$item->id}}','manager_class_{{$item->id}}')" type="checkbox" /> {{$item->name }} </label>
    <fieldset class="form-group">
        <div class="row fieldset">

            @foreach($item->permission as $key => $value)
            <div class="form-check col-md-2">
                @if(isset($role_permission))
                <input class="form-check-input selectedId module_checkbox_class manager_class_{{$value->module_id}}" data-manager_checkbox_class='manager_class_{{$value->module_id}}' @if(in_array($value->id, $role_permission)) checked @endif id="cbx-group-{{$item->id}}-{{$value->id}}" type="checkbox" name = "permission_id[]" value="{{$value->id}}" />
                @else
                <input class="form-check-input selectedId module_checkbox_class manager_class_{{$value->module_id}}" data-manager_checkbox_class='manager_class_{{$value->module_id}}' id="cbx-group-{{$item->id}}-{{$value->id}}" type="checkbox" name="permission_id[]" value="{{$value->id}}" />
                @endif
                <label class="form-check-label">{{$value->name}} </label>
            </div>
            @endforeach
        </div>

    </fieldset>
    @endforeach
    @endif

</div>
<button type="submit" class="btn btn-primary mt-2 submit_btn buttonload"> {{__('Submit')}} </button>
<script>
    $(document).ready(function() {
        $('#selectall').click(function() {
            $('.selectedId').prop('checked', this.checked);
        });

        $('.selectedId').change(function() {
            var check = ($('.selectedId').filter(":checked").length == $('.selectedId').length);
            $('#selectall').prop("checked", check);
        });

       
    });
    function checkBoxModule(module_class,module_checkbox_class) {
        $('#' + module_class).change(function() {
          $('.' + module_checkbox_class).prop('checked', this.checked);
      });
      $('.' + module_checkbox_class).change(function() {
          var check = ($('.' + module_checkbox_class).filter(":checked").length == $('.' + module_checkbox_class).length);
          $('#' + module_class).prop("checked", check);
      });
  }

   
</script>