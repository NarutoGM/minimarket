<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import WarningButton from '@/Components/WarningButton.vue';
import Modal from '@/Components/Modal.vue';
import DarkButton from '@/Components/DarkButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import {readonly, ref} from 'vue';
import { Head , useForm} from '@inertiajs/vue3';
import InputGroup from '@/Components/InputGroup.vue';
import SelectInput from '@/Components/SelectInput.vue';

import PrimaryButton from '@/Components/PrimaryButton.vue';
import vueTailwindPaginationUmd from '@ocrv/vue-tailwind-pagination';

import Swal from 'sweetalert2';
const props= defineProps({
	producto:{type:Object},flash:{type:Object},categoria:{type:Object} // Asegúrate de que `timeDifference` esté definido como prop	
});
const form = useForm({producto:'',precio:'',stock:'',categoria:'',idCategoria:'',numero:'',imagen: null,idProducto: ''});

const v = ref({	producto:'',idCategoria:'',precio:'',stock:''});


const showModalForm = ref(false);
const showModalForm2 = ref(false);

let operation = ref(1);

let imagen = null;


const title = ref('');




const msj = ref((props.flash.success != null) ? props.flash.success: '');
const classMsj = ref((props.flash.success != null) ? ' ' : 'hidden');

const openModalForm = (op,b) =>{


operation = op;
if (op==1){
    title.value='CREAR UN NUEVO PRODUCTO';
    showModalForm.value = true;

}else{
    if (op==2){
    showModalForm.value = true;

    title.value='EDITAR INFORMACIÓN';
    form.idCategoria=b.idCategoria;
    form.categoria=b.categoria;
    form.producto=b.producto;
    form.precio=b.precio;
   // form.stock=b.stock;
    form.idProducto=b.idProducto;
    form.numero=1;
    form.imagen=b.imagen
    }else{
      showModalForm2.value = true;

    title.value='AÑADIR EXISTENCIAS ';
    form.idCategoria=b.idCategoria;
    form.producto=b.producto;
    form.precio=b.precio;
   // form.stock=b.stock;
    form.idProducto=b.idProducto;
    form.categoria=b.categoria.categoria;
    form.numero=2;

    }


}
 
}



const deletedProductos= (idProducto,producto) =>{
    const alerta=Swal.mixin({
       buttonsStyling:true
    });
    alerta.fire({

        title:'Desea eliminar el producto? '+  producto +' ? ',
        icon:'question', showCancelButton:true,
        confirmButtonText:'<i class="fa-solid fa-check"></i> Si, eliminar',
        cancelButtonText:'<i class="fa-solid fa-ban"></i> Cancelar',
    }).then((result) => {
        if(result.isConfirmed) {
		    
            form.delete(route('productos.destroy',idProducto),{
                onSuccess: () => {ok('Categoria eliminada')}
            });
			
        }
    });
}


const closeModalForm = () =>{
	showModalForm.value = false;
	form.reset();
    imagen='';
}
const closeModalForm2 = () =>{
	showModalForm2.value = false;
	form.reset();

}

const save = () => { 
    if (operation == 1) {

        form.post(route('productos.store'), {
            onSuccess: () => { ok('Producto creado') }
        });
        
    } else {

        if (operation == 2) {
        form.post(route('updateprod'), {
        onSuccess: () => { ok('Producto editado')}
        });

        }else{
            form.post(route('updateprod'), {
                onSuccess: () => { ok('Entradas añadidas con exito') }
            });
        }
        
    }
};


const ok = (m) => {
    
	if(operation == 2 || operation ==1 ){
        closeModalForm();
	 }

     if(operation ==3){
        closeModalForm2();
	 }
	 form.reset();
	 msj.value = m;
	 classMsj.value = 'block'
	 setTimeout(() => {
		classMsj.value= 'hidden'
	 }, 5000);
}

const formPage = useForm({});

const onPageClick = (event)=>{
    formPage.get(route('productos.index',{page:event}));
}
            
const handleImageUpload = (event) => {
    
    const file = event.target.files[0];
      if (file) {

        console.log(file);

        const reader = new FileReader();
        reader.onload = (e) => {
            imagen = e.target.result; // Almacena la URL base64 en `form.imagen`
            form.imagen = file;

           // console.log(form.imagen);
        };
        reader.readAsDataURL(file); // Convierte el archivo a Base64
      } else {
        console.log('No se seleccionó un archivo.');
      }
};

</script>

<template>
	<Head title="PRODUCTO"/>
	
	<AuthenticatedLayout>
		<template #header>
			GESTION STOCK Y PRODUCTOS //
		</template>

		<DIV class="pb-2 flex justify-between items-center">

			
<DarkButton @click="openModalForm(1) "> <div class="flex"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
<P class="text-xl mx-2">NUEVO REGISTRO</P> 
	</div>

    
		</DarkButton>
		<vueTailwindPaginationUmd class="relative" 
       :current="producto.currentPage" :total="producto.total" 
       :per-page="producto.perPage"
       @page-changed="$event => onPageClick($event)"
       ></vueTailwindPaginationUmd>
	   

		</DIV>


		<div class="inline-flex overflow-hidden mb-4 w-full  bg-white rounded-lg shadow-md" :class="classMsj">
				<div class="flex justify-center items-center w-12 bg-green-500">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>

				</div>
				
				<div class="px-4 py-2 -mx-3">
					<div class="mx-3">
						<span class="font-semibold text-green-500">Success</span>
						<p class="text-sm text-gray-600">{{msj}}</p>
					</div>
				</div>
			</div>		
			
			

			<div class="w-full overflow-hidden rounded-lg border shadow-md">
    <div class="w-full overflow-x-auto bg-white">
        <table class="w-full whitespace-nowrap">
            <thead>
                <tr class="text-sm font-semibold tracking-wide text-gray-100 uppercase border-b bg-blue-800">
                    <th class="px-1 py-3 text-center">PRODUCTO</th>

                    <th class="px-1 py-3 text-center">NOMBRE COMERCIAL</th>
                    <th class="px-1 py-3 text-center">CATEGORIA</th>
                    <th class="px-1 py-3 text-center">PRECIO S/.</th>
                    <th class="px-1 py-3 text-center">STOCK</th>
                    <th class="px-1 py-3 text-center">EDITAR</th>
                    <th class="px-1 py-3 text-center">BORRAR</th>
                    <th class="px-1 py-3 text-center">AÑADIR STOCK</th>

                </tr>
            </thead>
            <tbody class="text-gray-900 font-bold divide-y dark:divide-gray-700 dark:bg-blue-50">
                <tr v-for="(b, i) in producto.data" :key="b.idProducto" class="text-gray-500">
                    <td class="px-4 py-3 text-center">
    <div class="flex justify-center">
        <img v-if="b.imagen" :src="`/storage/${b.imagen}`" alt="Imagen del Producto" class="h-24 w-24 object-cover">
    </div>
</td>


                    <td class="px-1 py-3 text-sm text-center">{{ b.producto }}</td>
                    <td class="px-1 py-3 text-sm text-center">{{ b.categoria.categoria }}</td>
                    <td class="px-1 py-3 text-sm text-center">{{ b.precio }}</td>

                    <td class="px-1 py-3 text-sm text-center text-gray-700" :class="{  'bg-red-400': b.stock < 20,  'bg-green-400': b.stock >= 20  }"> {{ b.stock }}</td>
                    
                    <td class="px-1 py-3 text-sm text-center">
                        <WarningButton @click="openModalForm(2, b)" >

                    
    
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                            </svg>
                        </WarningButton>
                    </td>

                    <td class="px-1 py-3 text-sm text-center">
                        <DeleteButton @click="$event => deletedProductos(b.idProducto,b.producto)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                        </DeleteButton>
                    </td>

                    <td class="px-1 py-3 text-sm text-center">
                        <DarkButton @click="$event => openModalForm(3,b)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                        </DarkButton>
                    </td>

                    
                </tr>
            </tbody>
        </table>
    </div>
</div>
<Modal :show="showModalForm" @close="closeModalForm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">{{ title }}</h2>
        <div class="m-6 mb-6 space-y-6 max-w-xl ">

            <span>NOMBRE DEL PRODUCTO: </span>
            <InputGroup :text="'NOMBRE DEL PRODUCTO'" :require="'required'" v-model="form.producto" :type="'text'"></InputGroup>   
            <br>
            <InputError class="mt-1" :message="form.errors.producto"></InputError>

            <span>CATEGORIA: </span>
            <SelectInput :text="'CATEGORIA'" :require="'required'" v-model="form.idCategoria" :type="'text'" :options="categoria"></SelectInput>
            <br>
            <InputError class="mt-1" :message="form.errors.idCategoria"></InputError>

            <span>PRECIO: </span>
            <InputGroup :text="'PRECIO'" :require="'required'" v-model="form.precio" :type="'number'" step="0.01"></InputGroup>
            <br>
            <InputError class="mt-1" :message="form.errors.precio"></InputError>

            <!-- Nuevo campo para subir imagen -->
            <span>IMAGEN DEL PRODUCTO: </span>

            <img v-if="form.imagen && !imagen"  :src="`/storage/${form.imagen}`" alt="Imagen del Producto" class="h-24 w-24 object-cover">
            <img v-if="imagen" :src="imagen" alt="Vista previa de la imagen" class="h-24 w-24 object-cover">

            <input type="file" @change="handleImageUpload">
            <br>
            
            
            <InputError class="mt-1" :message="form.errors.imagen"></InputError>


            <PrimaryButton @click="save">Guardar</PrimaryButton>
        </div>
    </div>
    <div class="m-6 flex justify-end">
        <SecondaryButton @click="closeModalForm">Cancel</SecondaryButton>
    </div>


</Modal>


   
        <Modal :show="showModalForm2" @close="closeModalForm2">
			<div class="p-6">
                 <h2 class="text-lg font-medium text-gray-900">{{ title }}</h2>
				 <div class="m-6 mb-6 space-y-6 max-w-xl ">


                    <span >NOMBRE DEL PRODUCTO: </span>
                    <InputGroup :text="'NOMBRE DEL PRODUCTO'" :readonly="readonly" :require="'required'" v-model="form.producto" :type="'text'">
						
                    
                       -


					</InputGroup>   

					<br>
					<InputError class="mt-1" :readonly="readonly" :message="form.errors.producto"></InputError>

                    <span >CATEGORIA: </span>
                    <InputGroup  :readonly="readonly" :text="'CATEGORIA'" :require="'required'" v-model="form.categoria" :type="'text'">
                    
					</InputGroup>

                    
                    <br>
					<InputError class="mt-1"  :message="form.errors.categoria"></InputError>

                    <span  :readonly="readonly">PRECIO: </span>

                    <InputGroup  :readonly="readonly" :text="'PRECIO'" :require="'required'" v-model="form.precio" :type="'number'" step="0.01">
						
                        -



					</InputGroup>
                    <br>
					<InputError class="mt-1" :message="form.errors.precio"></InputError>

                    

                    <span >STOCK: </span>

                    <InputGroup  :text="''" :require="'required'" v-model="form.stock" :type="'number'">
                        
                    </InputGroup>

                    <InputError class="mt-1" :message="form.errors.stock"></InputError>

                                        

					

					<PrimaryButton @click="save">Guardar</PrimaryButton>
				 </div>
			</div>
			<div class="m-6 flex justify-end">
				<SecondaryButton @click="closeModalForm2">Cancel</SecondaryButton>
			</div>
		</Modal>
   
		
	</AuthenticatedLayout>
</template>
