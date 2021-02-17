create procedure sp_tblProductos_listar(
	in	pEstado bit,
	in pId int
)
begin

	select
	t.id,
	t.nombre,
	t.descripcion ,
	t.estado,
	t.precio ,
	t.imagen ,
	t.creadoPor ,
	t.created_at ,
	t.updated_at
	from tblproductos t
	where t.estado = (case when pEstado is null then  t.estado else pEstado  end)
	and t.id = (case when pId is null then  t.id else pId  end );

end



create procedure sp_tblProductos_nuevo(
	in	pNombre varchar(100),
	in pDescripcion text,
	in pPrecio decimal(10,0),
	in pImagen text,
	in pIdCategoria int,
	in pUsuario int
)
begin

	declare vIdProducto int;

	declare exit handler for sqlexception
    begin
		rollback;
        resignal;

	end ;

	declare exit handler for sqlwarning
    begin
	    rollback;
        resignal;


    end ;



   if( exists(select 1 from tblproductos where nombre = pNombre) ) then
   		signal SQLSTATE '45000' set message_text='Ya existe un producto con ese nombre.';
   end if;


	start transaction;
	  insert into tblproductos
		(
		  nombre,
		  descripcion,
		  precio ,
		  imagen,
		  idCategoria,
		  creadoPor
		)
		values
		(
			pNombre,
			pDescripcion,
			pPrecio,
			pImagen,
			pIdCategoria,
			pUsuario
		);
	commit;

	select last_insert_id() into vIdProducto ;
	call sp_tblProductos_listar(1,vIdProducto);

end;


create procedure sp_tblProductos_actualizar(
	in  pId int,
	in	pNombre varchar(100),
	in pDescripcion text,
	in pPrecio decimal(10,0),
	in pIdCategoria int,
	in pEstado bit,
	in pUsuario int,
	in pSoloEstado bit
)
begin



	declare exit handler for sqlexception
    begin
		rollback;
        resignal;

	end ;

	declare exit handler for sqlwarning
    begin
	    rollback;
        resignal;


    end ;



   if( not exists(select 1 from tblproductos where id=pId) ) then
   		signal SQLSTATE '45000' set message_text='No se encontro producto a actualizar';
   end if;

  	start transaction;

    if (pSoloEstado =1 ) then


    		update tblproductos set estado = pEstado where id =pId;



    else


				update tblproductos
				set
				nombre = pNombre,
				descripcion = pDescripcion,
				precio = pPrecio,
				idCategoria = pIdCategoria,
				updated_at = current_timestamp()
				where id =pId;




    end if;

	call sp_tblProductos_listar(null,pId);
	commit;

end


create procedure sp_tblProductos_eliminar(
	in  pId int,
	in pUsuario int,
	in pSoloDesactivar bit
)
begin



	declare exit handler for sqlexception
    begin
		rollback;
        resignal;

	end ;

	declare exit handler for sqlwarning
    begin
	    rollback;
        resignal;


    end ;



   if( not exists(select 1 from tblproductos where id=pId) ) then
   		signal SQLSTATE '45000' set message_text='No se encontro producto a eliminar';
   end if;


   if(pSoloDesactivar =1 ) then
   		start transaction;
   		update tblproductos set estado = 0  where id=pId;
   		commit;
  	else


    	start transaction;

    		delete from tblproductos where id =pId;

    	commit;
    end if;


end


create procedure sp_tblProductos_actualizarFoto(
	in  pId int,
	in pUsuario int,
	in pNuevaUrlFoto text
)
begin



	declare exit handler for sqlexception
    begin
		rollback;
        resignal;

	end ;

	declare exit handler for sqlwarning
    begin
	    rollback;
        resignal;


    end ;



   if( not exists(select 1 from tblproductos where id=pId) ) then
   		signal SQLSTATE '45000' set message_text='No se encontro producto';
   end if;



   		start transaction;

   			update tblproductos set imagen = pNuevaUrlFoto  where id=pId;

   		commit;

   		call sp_tblProductos_listar(null,pId);


end



