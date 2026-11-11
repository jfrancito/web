<div class="input-group">
      <span class="input-group-addon" style="padding:0px;">
        {!! Form::select( 'serie_guia', $combo_serie_guia, array($item->nro_serie),
        [
          'class'       => 'form-control control input-tabla' ,
          'id'          => 'serie_guia',
          'style'       => 'width: 85px;height: 24px;padding: 3px 9px;',
          'required'    => '',
          'data-aw'     => '2',
          $disabled_guia
        ]) !!}
      </span>
        <input type="text"
         name="nro_documento"
         value="{{$item->nro_documento}}"
         class="form-control input-sm dineronrodoc nro_documento" {{$disabled_guia}}
        >
</div>