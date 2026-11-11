<div class='listaproductos'>

  <input type="hidden" name="txt_producto_obsequio" id = "txt_producto_obsequio" value="1">

  <table id="tableproductotp" class="table table-striped table-hover table-fw-widget">
    <thead>
      <tr>
        <th>PRODUCTOS</th>
      </tr>
    </thead>
    <tbody>
      @foreach($listaproductos as $item)
        <tr class='filaproducto'
            data_ipr='{{Hashids::encode(substr($item->COD_PRODUCTO, -13))}}'
            data_ppr='{{substr($item->COD_PRODUCTO, 0, 3)}}'
            data_spr='{{$item->COD_CATEGORIA_SUB_FAMILIA}}'
            data_npr='{{$item->NOM_PRODUCTO}}'
            data_upr='{{$item->NOM_UNIDAD_MEDIDA}}'
            data_mpr='{{$item->CAN_PESO_MATERIAL}}'
            data_epr='{{$item->CAN_EMPAQUE}}'>
          <td class="cell-detail">
            <span>{{$item->NOM_PRODUCTO}}</span>
            <span class="cell-detail-description-producto">{{$item->NOM_UNIDAD_MEDIDA}} </span>
          </td>
        </tr>                    
      @endforeach

    </tbody>
  </table>
</div>

<div class="row precioproducto">
  <div class="col-sm-12 col-mobil-top ajaxreglaproducto">
    <div class="panel panel-contrast">
      <div class="panel-heading panel-heading-contrast">
            <strong class='p_nombre_producto'>Nombre producto</strong>
            <span class="panel-subtitle p_unidad_medida">unidad medida</span>
            <span class="panel-subtitle p_peso_producto">Peso producto</span>                          
            <span class="mdi mdi-close-circle mdi-close-precio"></span>
            <span class="mdi mdi-check-circle mdi-check-precio"
              data_ipr=''
              data_ppr=''
              data_npr=''
              data_upr=''
              data_mpr=''
              data_spr=''
              data_epr=''
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
                      onkeyup="handleEvt(event, this, '')"/>
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
                      placeholder="Número de Paquetes"
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

    </div>
</div>

