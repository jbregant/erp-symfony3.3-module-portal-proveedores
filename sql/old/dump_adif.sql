INSERT INTO adif_proveedores.tipo_proveedor (id, extranjero, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, 0, "Persona física", NOW(), NOW() ),
(2, 0, "Persona jurídica", NOW(), NOW() ),
(3, 0, "Contratos de Colaboración Empresaria", NOW(), NOW() ),
(4, 1, "Persona física extranjera no residente en el país", NOW(), NOW() ),
(5, 1, "Persona jurídica extranjera", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_persona (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "Persona física", NOW(), NOW() ),
(2, "Persona jurídica", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_persona_juridica (id, id_tipo_persona, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, 2, "Empresarios o empresas individuales", NOW(), NOW() ),
(2, 2, "Sociedad Civil", NOW(), NOW() ),
(3, 2, "Fundación", NOW(), NOW() ),
(4, 2, "Asociación Civil", NOW(), NOW() ),
(5, 2, "Sociedades de Hecho", NOW(), NOW() ),
(6, 2, "Sociedad en Comandita Simple", NOW(), NOW() ),
(7, 2, "Sociedad de Capital e Industria", NOW(), NOW() ),
(8, 2, "Sociedad Accidental o en participación Sociedades por cuotas", NOW(), NOW() ),
(9, 2, "Sociedad de Responsabilidad Limitada Sociedades por acciones", NOW(), NOW() ),
(10, 2, "Sociedad Anónima", NOW(), NOW() ),
(11, 2, "Sociedad Anónima con Participación Estatal Mayoritaria", NOW(), NOW() ),
(12, 2, "Sociedad en Comandita por Acciones (arts. 315 a 324, LSC)", NOW(), NOW() ),
(13, 2, "Entidades Cooperativas", NOW(), NOW() ),
(14, 2, "Sociedad del Estado", NOW(), NOW() ),
(15, 2, "Otros Organismos Estatales", NOW(), NOW() ),
(16, 2, "Sociedad por acciones simplificada (SAS)", NOW(), NOW() ),
(17, 2, "Sucursales o Establecimientos Permanentes", NOW(), NOW() );

INSERT INTO adif_proveedores.estado_evaluacion (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "Pendiente", NOW(), NOW()) ,
(2, "Aprobado", NOW(), NOW()) ,
(3, "Rechazado", NOW(), NOW()) ,
(4, "Observado", NOW(), NOW()) ;

INSERT INTO adif_proveedores.estado_evaluacion_gerencia (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "Pendiente", NOW(), NOW()) ,
(2, "Aprobado", NOW(), NOW()) ,
(3, "Rechazado", NOW(), NOW()) ,
(4, "Observado", NOW(), NOW()) ;

INSERT INTO adif_proveedores.gerencia (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "GAL", NOW(), NOW() ),
(2, "GALO", NOW(), NOW() ),
(3, "GCSHM", NOW(), NOW() ),
(4, "GAF-Finanzas", NOW(), NOW() ),
(5, "GAF-Impuestos", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_domicilio (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "Domicilio Real", NOW(), NOW() ),
(2, "Domicilio Legal", NOW(), NOW() ),
(3, "Domicilio Fiscal", NOW(), NOW() ),
(4, "Domicilio Contractual", NOW(), NOW() ),
(5, "Domicilio Exterior", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_documentacion (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1,"proveedor_actividad", NOW(), NOW() ),
(2,"proveedor_rubro", NOW(), NOW() ),
(3,"proveedor_domicilio_legal", NOW(), NOW() ),
(4,"proveedor_domicilio_fiscal", NOW(), NOW() ),
(5,"proveedor_domicilio_contraactual", NOW(), NOW() ),
(6,"proveedor_representante_apoderado", NOW(), NOW() ),
(7,"proveedor_datos_impositivos", NOW(), NOW() ),
(8,"proveedor_datos_impositivos_iva", NOW(), NOW() ),
(9,"proveedor_datos_impositivos_suss", NOW(), NOW() ),
(10,"proveedor_datos_impositivos_ganancias", NOW(), NOW() ),
(11,"proveedor_datos_impositivos_iibb", NOW(), NOW() ),
(12,"proveedor_datos_impositivos_constancia_inscripcion", NOW(), NOW() ),
(13,"proveedor_datos_bancarios", NOW(), NOW() ),
(14,"proveedor_datos_bancarios_cbu", NOW(), NOW() ),
(15,"proveedor_gcshm", NOW(), NOW() ),
(16,"miembros_ute", NOW(), NOW() ),
(17,"proveedor_persona_juridica", NOW(), NOW() ),
(18,"convenio_unilateral", NOW(), NOW() ),
(19,"tributacion_internacional", NOW(), NOW() ),
(20,"establecimiento_argentina", NOW(), NOW() ),
(21,"proveedor_persona_fisica", NOW(), NOW() ),
(22,"proveedor_domicilio_real", NOW(), NOW() ),
(23,"proveedor_domicilio_exterior", NOW(), NOW() ),
(24,"proveedor_persona_fisica_extranjera", NOW(), NOW() ),
(25,"proveedor_persona_juridica_extranjera", NOW(), NOW() ),
(26,"proveedor_gcshm_iso9001", NOW(), NOW() ),
(27,"proveedor_gcshm_iso14001", NOW(), NOW() ),
(28,"proveedor_gcshm_osha18001", NOW(), NOW() );


INSERT INTO adif_proveedores.tipo_iva (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "Inscripto", NOW(), NOW() ),
(2, "No Inscripto", NOW(), NOW() ),
(3, "Monotributista", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_iva_inscripto (id, id_tipo_iva , denominacion, fecha_creacion , fecha_ultima_actualizacion ) VALUES
(1,1, "Local", NOW(), NOW() ),
(2,1, "Monotributista", NOW(), NOW() ),
(3,1, "Convenio Multilateral", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_timeline (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES
(1, "timeline_dato_contacto", NOW(), NOW() ),
(2, "timeline_persona_fisica", NOW(), NOW() ),
(3, "timeline_persona_juridica", NOW(), NOW() ),
(4, "timeline_contratos_ute", NOW(), NOW() ),
(5, "timeline_actividades", NOW(), NOW() ),
(6, "timeline_rubro", NOW(), NOW() ),
(7, "timeline_domicilio_real", NOW(), NOW() ),
(8, "timeline_domicilio_legal", NOW(), NOW() ),
(9, "timeline_domicilio_fiscal", NOW(), NOW() ),
(10, "timeline_domicilio_exterior", NOW(), NOW() ),
(11, "timeline_domicilio_contractual", NOW(), NOW() ),
(12, "timeline_representantes_apoderados", NOW(), NOW() ),
(13, "timeline_datos_impositivos", NOW(), NOW() ),
(14, "timeline_datos_bancarios", NOW(), NOW() ),
(15, "timeline_gcshm", NOW(), NOW() ),
(16, "timeline_ddjj", NOW(), NOW() ),
(17, "timeline_persona_fisica_extranjera", NOW(), NOW() ),
(18, "timeline_persona_juridica_extranjera", NOW(), NOW() );

INSERT INTO adif_proveedores.tipo_observacion (id, denominacion, fecha_creacion, fecha_ultima_actualizacion) VALUES 
(1,'datos_contacto', NOW(), NOW() ),
(2,'rubro', NOW(), NOW() ),
(3,'actividad', NOW(), NOW() ),
(4,'domicilio_real', NOW(), NOW() ),
(5,'domicilio_legal', NOW(), NOW() ),
(6,'domicilio_fiscal', NOW(), NOW() ),
(7,'domicilio_contractual', NOW(), NOW() ),
(8,'domicilio_exterior', NOW(), NOW() ),
(9,'representante_apoderado', NOW(), NOW() ),
(10,'datos_bancarios', NOW(), NOW() ),
(11,'datos_persona_fisica', NOW(), NOW() ),
(12,'datos_persona_juridica', NOW(), NOW() ),
(13,'datos_ute', NOW(), NOW() ),
(14,'datos_persona_fisica_extranjera', NOW(), NOW() ),
(15,'datos_persona_Juridica_extranjera', NOW(), NOW() ),
(16,'datos_impositivos', NOW(), NOW() ),
(17,'iva', NOW(), NOW() ),
(18,'suss', NOW(), NOW() ),
(19,'ganancias', NOW(), NOW() ),
(20,'ingresos_brutos', NOW(), NOW() ),
(21,'datos_gcshm', NOW(), NOW() );
