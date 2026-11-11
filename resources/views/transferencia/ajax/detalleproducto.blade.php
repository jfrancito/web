
<div class="panel panel-contrast">
      <div class="panel-heading panel-heading-contrast">
            <strong class='p_nombre_producto'>{{$data_npr}}</strong>
            <span class="panel-subtitle p_unidad_medida">{{$data_upr}}</span>
            <span class="panel-subtitle p_peso_producto">{{$data_mpr}} {{$data_spr}}</span>

            <span class="mdi mdi-close-circle mdi-close-precio"></span>
            <span class="mdi mdi-check-circle mdi-check-precio"
              data_ipr='{{$data_ipr}}'
              data_ppr='{{$data_ppr}}'
              data_npr='{{$data_npr}}'
              data_upr='{{$data_upr}}'
              data_mpr='{{$data_mpr}}'
              data_spr='{{$data_spr}}'
              data_epr='{{$data_epr}}'
            ></span>
      </div>
     
    </div>

    <div class="panel-body">
    
      <div class="col-sm-12">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            Cantidad
          </label>
          <div class="col-sm-12">
            <div class="input-group_mobil">
              <input  type="text"
                      id="cantidad" name='cantidad' 
                      value="" 
                      placeholder="Cantidad"
                      required = "" class="form-control input-sm importe" data-parsley-type="number"
                      autocomplete="off" data-aw="1"
                      onkeyup="handleEvt(event,this,'')" />
            </div>
          </div>
        </div>
      </div>

       <div class="col-sm-12">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            Paquetes
          </label>
          <div class="col-sm-12">
            <div class="input-group_mobil">
              <input  type="text"
                      id="paquete" name='paquete' 
                      value="" 
                      placeholder="Número de paquetes"
                      required = "" class="form-control input-sm importe" data-parsley-type="number"
                      autocomplete="off" data-aw="2"/>
            </div>
          </div>
        </div>
      </div>

       <div class="col-sm-12">
        <div class="form-group">
          <label class="col-sm-12 control-label">
            Peso Total
          </label>
          <div class="col-sm-12">
            <div class="input-group_mobil">
              <input  type="text"
                      id="pesototal" name='pesototal' 
                      value="" 
                      placeholder="Peso Total"
                      required = "" class="form-control input-sm importe" data-parsley-type="number"
                      autocomplete="off" data-aw="2"/>
            </div>
          </div>
        </div>
      </div>
      
    </div>

<script type="text/javascript">
    $('.importe').inputmask({ 'alias': 'numeric', 
    'groupSeparator': ',', 'autoGroup': true, 'digits': 4, 
    'digitsOptional': false, 
    'prefix': '', 
    'placeholder': '0'});
</script> 