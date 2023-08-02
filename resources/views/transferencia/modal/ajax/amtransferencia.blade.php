
<div class="modal-header" style = "padding: 12px !important;">
  <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
  <h3 class="modal-title">
    <strong>
      
    </strong>
  </h3>
  <input type="hidden" name="cuenta_id_m" id='cuenta_id_m' value='{{$centroorigen_id}}'>
</div>

<input type="hidden" name="tabestado" id='tabestado' value='ocen'>
<input type="hidden" name="idpicking" id='idpicking' value={{$idpicking}}>

<div class="modal-body modal-pedido-poc" style = "padding: 0px !important;">
  <div class="scroll_text scroll_text_heigth_poc" style = "padding: 0px !important;"> 
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="tab-container">
          <ul class="nav nav-tabs">
            <li class="seltab active" data_tab='ocen'>
              <a href="#ordencen" data-toggle="tab">Solicitud Transferencia</a>
            </li>
            <li class="seltab" data_tab='ord'>
              <a href="#ordenventas" data-toggle="tab">Orden Venta</a>
            </li>
            <li class="seltab" data_tab='prod'>
              <a href="#producto" data-toggle="tab">Productos</a>
            </li>
          </ul>

          <div class="tab-content" style = "padding: 0px !important;">
            <div id="ordencen" class="tab-pane active cont">
              @include('transferencia.modal.ajax.amlistatransferencia')
            </div>
            <div id="ordenventas" class="tab-pane cont">
              @include('transferencia.modal.ajax.amlistaordenventa')
            </div>
            <div id="producto" class="tab-pane  cont">
             @include('transferencia.modal.ajax.amlistaproductos')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal-footer">
  <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancelar</button>
  <button type="submit" data-dismiss="modal" class="btn btn-success" id="agregarproductos">Agregar Productos</button>
</div>

<script type="text/javascript">

    $(document).ready(function(){      
        $('.importe').inputmask({ 'alias': 'numeric', 
        'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 
        'digitsOptional': false, 
        'prefix': '', 
        'placeholder': '0'});
    });

  </script> 