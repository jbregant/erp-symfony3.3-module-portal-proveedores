SET GLOBAL event_scheduler = ON;
DELIMITER $$
DROP EVENT IF EXISTS adif_proveedores.event_invitacion_caducada$$
CREATE EVENT adif_proveedores.event_invitacion_caducada
    ON SCHEDULE
    EVERY 1 HOUR
DO
UPDATE adif_proveedores.invitacion 
SET caducada = 1,
    fecha_baja = NOW()
WHERE fecha_baja IS NULL
    AND TIMESTAMPDIFF(HOUR, fecha_alta, NOW()) > 24;
$$
DELIMITER ;
    
USE adifprod_contable;

DELIMITER $$
DROP FUNCTION IF EXISTS get_descripcion_CC$$
CREATE FUNCTION get_descripcion_CC(id_comprobante int)
RETURNS VARCHAR(255) CHARSET utf8
BEGIN
DECLARE descripcion VARCHAR(255);
SELECT r.descripcion
INTO descripcion
FROM (
    (SELECT CONCAT(IF(l.id_tipo_contratacion IS NOT NULL, CONCAT(tc.alias, ' '), ''), CONCAT(LPAD(l.numero, 3, '0'), '/', l.anio), ' - ', UPPER(ot.descripcion)) AS descripcion
    FROM adifprod_contable.comprobante_obra AS co
    INNER JOIN adifprod_contable.documento_financiero AS df
        ON df.id = co.id_documento_financiero
    INNER JOIN adifprod_contable.obras_tramo AS ot 
        ON ot.id = df.id_tramo
    INNER JOIN adifprod_contable.licitacion AS l 
        ON ot.id_licitacion = l.id
    INNER JOIN adifprod_compras.tipo_contratacion AS tc 
        ON l.id_tipo_contratacion = tc.id
    WHERE co.id = id_comprobante)

    UNION 

    (SELECT 'Comprobante de servicio' AS descripcion
    FROM adifprod_compras.orden_compra AS oc
    INNER JOIN adifprod_contable.comprobante_compra AS cc
        ON cc.id_orden_compra = oc.id
    WHERE oc.id_cotizacion IS NULL
        AND cc.id = id_comprobante)
    
    UNION
    
    (SELECT CONCAT('Orden de compra número ', LPAD(oc.numero_orden_compra, 8, '0')) AS descripcion
    FROM adifprod_compras.orden_compra AS oc
    INNER JOIN adifprod_contable.comprobante_compra AS cc
        ON cc.id_orden_compra = oc.id
    WHERE oc.id_cotizacion IS NOT NULL
        AND cc.id = id_comprobante)
        
    UNION
        
    (SELECT 'Anticipos sin ordenes de compra' AS descripcion
    WHERE id_comprobante IS NULL)
    
) AS r;
RETURN descripcion;

END $$
DELIMITER ;

DELIMITER $$
DROP VIEW IF EXISTS adifprod_contable.vw_cc_pagos_comprobantes$$
CREATE VIEW adifprod_contable.vw_cc_pagos_comprobantes AS 
SELECT 
    op.id AS id_orden_pago,
    pop.monto AS total_orden_pago,
    pago.id_estado_pago,
    ep.denominacion
FROM adifprod_contable.orden_pago AS op
LEFT JOIN adifprod_contable.pago_orden_pago AS pop
    ON pop.id = op.id_pago
LEFT JOIN (
    SELECT
        id_pago_orden_pago,
        id_estado_pago
    FROM adifprod_contable.cheque
    UNION ALL
    SELECT
        id_pago_orden_pago,
        id_estado_pago
    FROM adifprod_contable.transferencia_bancaria
    WHERE id_estado_pago <> 2
    ORDER BY id_pago_orden_pago asc, id_estado_pago desc
) AS pago
    ON pop.id = pago.id_pago_orden_pago
LEFT JOIN adifprod_contable.estado_pago AS ep
    ON pago.id_estado_pago = ep.id
LEFT JOIN adifprod_contable.estado_orden_pago AS eop 
    ON op.id_estado_orden_pago = eop.id
GROUP BY pago.id_pago_orden_pago;
$$
DELIMITER ;


DELIMITER $$
DROP VIEW IF EXISTS adifprod_contable.vw_cc_obras_servicios$$
CREATE VIEW adifprod_contable.vw_cc_obras_servicios AS 
SELECT 
    co.id_proveedor,
    IFNULL(ot.id, 0) AS grupo,
    adifprod_contable.get_descripcion_CC(c.id) AS descripcion,
    c.id AS id_comprobante,
    c.id_estado_comprobante AS id_estado_comprobante,
    ec.nombre AS estado_comprobante,
    co.punto_venta AS punto_venta,
    c.numero AS numero_comprobante,
    c.fecha_comprobante AS fecha_comprobante,
    c.fecha_creacion AS fecha_ingreso_comprobante,
    c.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_comprobante,
    tc.nombre AS tipo_comprobante,
    lc.letra AS letra_comprobante,
    IF(c.total IS NULL, 0, IF(c.id_tipo_comprobante = 3, c.total * -1, c.total)) AS total_comprobante,
    c.saldo AS saldo_comprobante,
    IFNULL(op.id, 0) AS id_orden_pago,
    IFNULL(op.numero_orden_pago, 0) AS numero_orden_pago,
    IFNULL(pago.total_orden_pago, 0) AS total_orden_pago,
    IFNULL(op.discriminador, c.discriminador) AS discriminador,
    op.fecha_orden_pago AS fecha_orden_pago,
    op.fecha_creacion AS fecha_creacion_orden_pago,
    op.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_orden_pago,
    op.id_estado_orden_pago AS id_estado_orden_pago,
    eop.denominacion AS estado_orden_pago,
    pago.id_estado_pago AS id_estado_pago,
    pago.denominacion AS estado_pago,
    NULL AS id_orden_pago_cancelada
FROM adifprod_contable.comprobante AS c 
INNER JOIN adifprod_contable.tipo_comprobante AS tc 
    ON c.id_tipo_comprobante = tc.id
LEFT JOIN adifprod_contable.letra_comprobante AS lc 
    ON c.id_letra_comprobante = lc.id
LEFT JOIN (
    SELECT 
        id, 
        id_orden_pago, 
        id_proveedor, 
        fecha_anulacion, 
        punto_venta,
        NULL AS id_documento_financiero
    FROM adifprod_contable.comprobante_compra
    UNION
    SELECT 
        id, 
        id_orden_pago, 
        id_proveedor, 
        fecha_anulacion, 
        punto_venta,
        id_documento_financiero
    FROM adifprod_contable.comprobante_obra
) AS co 
    ON c.id = co.id
INNER JOIN adifprod_contable.estado_comprobante AS ec
    ON c.id_estado_comprobante = ec.id
LEFT JOIN adifprod_contable.orden_pago AS op 
    ON co.id_orden_pago = op.id
LEFT JOIN adifprod_contable.vw_cc_pagos_comprobantes AS pago
    ON op.id = pago.id_orden_pago
LEFT JOIN adifprod_contable.estado_orden_pago AS eop 
    ON op.id_estado_orden_pago = eop.id
LEFT JOIN adifprod_compras.proveedor AS p 
    ON co.id_proveedor = p.id
LEFT JOIN adifprod_contable.documento_financiero AS df
    ON df.id = co.id_documento_financiero
LEFT JOIN adifprod_contable.obras_tramo AS ot 
    ON ot.id = df.id_tramo
WHERE c.fecha_baja IS NULL
    AND op.fecha_baja IS NULL
    AND NOT EXISTS (SELECT 1 
                    FROM adifprod_contable.comprobante_ajuste AS ca
                    WHERE c.id = ca.id_comprobante);
$$
DELIMITER ;

DELIMITER $$
DROP VIEW IF EXISTS adifprod_contable.vw_cc_pagos_parciales$$
CREATE VIEW adifprod_contable.vw_cc_pagos_parciales AS 
SELECT 
    pp.id_proveedor AS id_proveedor,
    IFNULL(ot.id, 0) AS grupo,
    adifprod_contable.get_descripcion_CC(c.id) AS descripcion,
    pp.id_comprobante AS id_comprobante,
    c.id_estado_comprobante AS id_estado_comprobante,
    ec.nombre AS estado_comprobante,
    0 AS punto_venta,
    c.numero AS numero_comprobante,
    c.fecha_comprobante AS fecha_comprobante,
    c.fecha_creacion AS fecha_ingreso_comprobante,
    c.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_comprobante,
    'Pago parcial' AS tipo_comprobante,
    '' AS letra_comprobante,
    IF(c.total IS NULL, 0, IF(c.id_tipo_comprobante = 3, c.total * -1, c.total)) AS total_comprobante,
    pp.importe AS saldo_comprobante,
    IFNULL(op.id, 0) AS id_orden_pago,
    IFNULL(op.numero_orden_pago, 0) AS numero_orden_pago,
    0 AS total_orden_pago,
    op.discriminador AS discriminador,
    pp.fecha_pago AS fecha_orden_pago,
    pp.fecha_creacion AS fecha_creacion_orden_pago,
    pp.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_orden_pago,
    op.id_estado_orden_pago AS id_estado_orden_pago,
    eop.denominacion AS estado_orden_pago,
    pago.id_estado_pago AS id_estado_pago,
    pago.denominacion AS estado_pago,
    NULL AS id_orden_pago_cancelada
FROM adifprod_contable.pago_parcial AS pp
INNER JOIN adifprod_contable.comprobante AS c
    ON pp.id_comprobante = c.id
INNER JOIN adifprod_contable.estado_comprobante AS ec
    ON c.id_estado_comprobante = ec.id
INNER JOIN adifprod_contable.orden_pago_pago_parcial AS oppp
    ON oppp.id_pago_parcial = pp.id
INNER JOIN adifprod_contable.orden_pago AS op
    ON op.id = oppp.id
INNER JOIN adifprod_contable.estado_orden_pago AS eop 
    ON eop.id = op.id_estado_orden_pago
INNER JOIN (
    SELECT id,
           id_orden_pago,
           id_proveedor,
           fecha_anulacion,
           punto_venta,
           NULL AS id_documento_financiero
    FROM   adifprod_contable.comprobante_compra
    UNION
    SELECT id,
           id_orden_pago,
           id_proveedor,
           fecha_anulacion,
           punto_venta,
           id_documento_financiero
    FROM   adifprod_contable.comprobante_obra
) AS cop
    ON c.id = cop.id 
    AND pp.id_proveedor = cop.id_proveedor
LEFT JOIN adifprod_compras.proveedor AS p 
    ON pp.id_proveedor = p.id
LEFT JOIN (
    SELECT op.id
    FROM adifprod_contable.orden_pago op
    WHERE op.fecha_contable <= now()
        AND op.id_estado_orden_pago = 4
        AND op.fecha_baja IS NULL 
) AS op_cancelada
    ON op_cancelada.id = cop.id_orden_pago
LEFT JOIN adifprod_contable.documento_financiero AS df
    ON df.id = cop.id_documento_financiero
LEFT JOIN adifprod_contable.obras_tramo AS ot 
    ON ot.id = df.id_tramo
LEFT JOIN adifprod_contable.vw_cc_pagos_comprobantes AS pago
    ON op.id = pago.id_orden_pago
WHERE pp.fecha_baja IS NULL
    AND op.fecha_baja IS NULL
    AND op.numero_orden_pago IS NOT NULL
    AND op.fecha_contable <= now()
    AND (op.id_estado_orden_pago = 4
        OR (op.id_estado_orden_pago = 5 AND op.fecha_anulacion IS NOT NULL)
    );
$$
DELIMITER ;

DELIMITER $$
DROP VIEW IF EXISTS adifprod_contable.vw_cc_anticipos_aplicados$$
CREATE VIEW adifprod_contable.vw_cc_anticipos_aplicados AS 
SELECT
    a.id_proveedor AS id_proveedor,
    IFNULL(ot.id, 0) AS grupo,
    adifprod_contable.get_descripcion_CC(cc.id) AS descripcion,
    c.id AS id_comprobante,
    c.id_estado_comprobante AS id_estado_comprobante,
    ec.nombre AS estado_comprobante,
    NULL AS punto_venta,
    c.numero AS numero_comprobante,
    a.fecha AS fecha_comprobante,
    a.fecha_creacion AS fecha_ingreso_comprobante,
    a.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_comprobante,
    'Anticipo' AS tipo_comprobante,
    NULL AS letra_comprobante,
    IF(c.total IS NULL, 0, IF(c.id_tipo_comprobante = 3, c.total * -1, c.total)) AS total_comprobante,
    a.monto AS saldo_comprobante,
    IFNULL(op.id, 0) AS id_orden_pago,
    IFNULL(op.numero_orden_pago, 0) AS numero_orden_pago,
    0 AS total_orden_pago,
    a.discriminador AS discriminador,
    NULL AS fecha_orden_pago,
    NULL AS fecha_creacion_orden_pago,
    NULL AS fecha_ultima_actualizacion_orden_pago,
    NULL AS id_estado_orden_pago,
    NULL AS estado_orden_pago,
    NULL AS id_estado_pago,
    NULL AS estado_pago,
    a.id_orden_pago_cancelada id_orden_pago_cancelada
FROM adifprod_contable.anticipo a
INNER JOIN adifprod_contable.orden_pago_anticipo_proveedor opa
    ON opa.id_anticipo = a.id
INNER JOIN adifprod_contable.orden_pago op
    ON op.id = opa.id
LEFT JOIN (
    SELECT op.id
    FROM adifprod_contable.orden_pago op
    WHERE op.fecha_baja IS NULL
        AND op.numero_orden_pago IS NOT NULL
        AND CAST(op.fecha_contable AS DATE) <= NOW()
        AND (op.id_estado_orden_pago = 4
            OR (op.id_estado_orden_pago = 5
                AND op.fecha_anulacion IS NOT NULL
                AND CAST(op.fecha_anulacion AS DATE) > NOW()
                AND op.id_asiento_contable_anulacion IS NOT NULL
            )
        )
) op_cancelada
    ON op_cancelada.id = a.id_orden_pago_cancelada
INNER JOIN adifprod_contable.comprobante_compra AS cc
    ON cc.id_orden_pago = op_cancelada.id
INNER JOIN adifprod_contable.comprobante AS c
    ON cc.id = c.id
INNER JOIN adifprod_contable.estado_comprobante AS ec
    ON c.id_estado_comprobante = ec.id
LEFT JOIN adifprod_contable.comprobante_obra AS co
    ON co.id = cc.id
LEFT JOIN adifprod_contable.documento_financiero AS df
    ON df.id = co.id_documento_financiero
LEFT JOIN adifprod_contable.obras_tramo AS ot 
    ON ot.id = df.id_tramo
WHERE a.fecha_baja IS NULL
    AND op.fecha_baja IS NULL
    AND op.numero_orden_pago IS NOT NULL
    AND CAST(op.fecha_contable AS DATE) <= NOW()
    AND op_cancelada.id IS NOT NULL
    AND (op.id_estado_orden_pago = 4
        OR (op.id_estado_orden_pago = 5
            AND CAST(op.fecha_anulacion AS DATE) > NOW()
        )
    );
$$
DELIMITER ;


DELIMITER $$
DROP VIEW IF EXISTS adifprod_contable.vw_cc_anticipos_no_aplicados$$
CREATE VIEW adifprod_contable.vw_cc_anticipos_no_aplicados AS 
SELECT 
    a.id_proveedor AS id_proveedor,
    99999999 AS grupo,
    adifprod_contable.get_descripcion_CC(NULL) AS descripcion,
    a.id AS id_comprobante,
    NULL AS id_estado_comprobante,
    NULL AS estado_comprobante,
    NULL AS punto_venta,
    a.id AS numero_comprobante,
    a.fecha AS fecha_comprobante,
    a.fecha_creacion AS fecha_ingreso_comprobante,
    a.fecha_ultima_actualizacion AS fecha_ultima_actualizacion_comprobante,
    'Anticipo' AS tipo_comprobante,
    NULL AS letra_comprobante,
    0 AS total_comprobante,
    a.monto AS saldo_comprobante,
    IFNULL(op.id, 0) AS id_orden_pago,
    IFNULL(op.numero_orden_pago, 0) AS numero_orden_pago,
    0 AS total_orden_pago,
    a.discriminador AS discriminador,
    NULL AS fecha_orden_pago,
    NULL AS fecha_creacion_orden_pago,
    NULL AS fecha_ultima_actualizacion_orden_pago,
    op.id_estado_orden_pago AS id_estado_orden_pago,
    NULL AS estado_orden_pago,
    NULL AS id_estado_pago,
    NULL AS estado_pago,
    NULL AS id_orden_pago_cancelada
FROM adifprod_compras.proveedor AS p
INNER JOIN adifprod_contable.anticipo AS a
    ON a.id_proveedor = p.id
INNER JOIN adifprod_contable.orden_pago_anticipo_proveedor AS opap
    ON opap.id_anticipo = a.id
INNER JOIN adifprod_contable.orden_pago AS op
    ON op.id = opap.id
WHERE a.id_orden_pago_cancelada IS NULL
    AND op.id_estado_orden_pago <> 5;
$$
DELIMITER ;