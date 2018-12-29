CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`%` 
    SQL SECURITY DEFINER
VIEW `vw_cc_obras_servicios` AS
    SELECT 
        `co`.`id_proveedor` AS `id_proveedor`,
        IFNULL(`ot`.`id`, 0) AS `grupo`,
        `adif_contable`.`get_descripcion_CC`(`c`.`id`) AS `descripcion`,
        `c`.`id` AS `id_comprobante`,
        `c`.`id_estado_comprobante` AS `id_estado_comprobante`,
        `ec`.`nombre` AS `estado_comprobante`,
        `co`.`punto_venta` AS `punto_venta`,
        `c`.`numero` AS `numero_comprobante`,
        `c`.`fecha_comprobante` AS `fecha_comprobante`,
        `co`.`fecha_ingreso_adif` AS `fecha_ingreso_comprobante`,
        `c`.`fecha_ultima_actualizacion` AS `fecha_ultima_actualizacion_comprobante`,
        `tc`.`nombre` AS `tipo_comprobante`,
        `lc`.`letra` AS `letra_comprobante`,
        IF(ISNULL(`c`.`total`),
            0,
            IF((`c`.`id_tipo_comprobante` = 3),
                (`c`.`total` * -(1)),
                `c`.`total`)) AS `total_comprobante`,
        `c`.`saldo` AS `saldo_comprobante`,
        IFNULL(`op`.`id`, 0) AS `id_orden_pago`,
        IFNULL(`op`.`numero_orden_pago`, 0) AS `numero_orden_pago`,
        IFNULL(`pago`.`total_orden_pago`, 0) AS `total_orden_pago`,
        IFNULL(`op`.`discriminador`,
                `c`.`discriminador`) AS `discriminador`,
        `op`.`fecha_orden_pago` AS `fecha_orden_pago`,
        `op`.`fecha_creacion` AS `fecha_creacion_orden_pago`,
        `op`.`fecha_ultima_actualizacion` AS `fecha_ultima_actualizacion_orden_pago`,
        `op`.`id_estado_orden_pago` AS `id_estado_orden_pago`,
        `eop`.`denominacion` AS `estado_orden_pago`,
        `pago`.`id_estado_pago` AS `id_estado_pago`,
        `pago`.`denominacion` AS `estado_pago`,
        NULL AS `id_orden_pago_cancelada`
    FROM
        ((((((((((`adif_contable`.`comprobante` `c`
        JOIN `adif_contable`.`tipo_comprobante` `tc` ON ((`c`.`id_tipo_comprobante` = `tc`.`id`)))
        LEFT JOIN `adif_contable`.`letra_comprobante` `lc` ON ((`c`.`id_letra_comprobante` = `lc`.`id`)))
        LEFT JOIN (SELECT 
            `adif_contable`.`comprobante_compra`.`id` AS `id`,
                `adif_contable`.`comprobante_compra`.`id_orden_pago` AS `id_orden_pago`,
                `adif_contable`.`comprobante_compra`.`id_proveedor` AS `id_proveedor`,
                `adif_contable`.`comprobante_compra`.`fecha_anulacion` AS `fecha_anulacion`,
                `adif_contable`.`comprobante_compra`.`fecha_ingreso_adif` AS `fecha_ingreso_adif`,
                `adif_contable`.`comprobante_compra`.`punto_venta` AS `punto_venta`,
                NULL AS `id_documento_financiero`
        FROM
            `adif_contable`.`comprobante_compra` UNION SELECT 
            `adif_contable`.`comprobante_obra`.`id` AS `id`,
                `adif_contable`.`comprobante_obra`.`id_orden_pago` AS `id_orden_pago`,
                `adif_contable`.`comprobante_obra`.`id_proveedor` AS `id_proveedor`,
                `adif_contable`.`comprobante_obra`.`fecha_anulacion` AS `fecha_anulacion`,
                `adif_contable`.`comprobante_obra`.`fecha_ingreso_adif` AS `fecha_ingreso_adif`,
                `adif_contable`.`comprobante_obra`.`punto_venta` AS `punto_venta`,
                `adif_contable`.`comprobante_obra`.`id_documento_financiero` AS `id_documento_financiero`
        FROM
            `adif_contable`.`comprobante_obra`) `co` ON ((`c`.`id` = `co`.`id`)))
        JOIN `adif_contable`.`estado_comprobante` `ec` ON ((`c`.`id_estado_comprobante` = `ec`.`id`)))
        LEFT JOIN `adif_contable`.`orden_pago` `op` ON ((`co`.`id_orden_pago` = `op`.`id`)))
        LEFT JOIN `adif_contable`.`vw_cc_pagos_comprobantes` `pago` ON ((`op`.`id` = `pago`.`id_orden_pago`)))
        LEFT JOIN `adif_contable`.`estado_orden_pago` `eop` ON ((`op`.`id_estado_orden_pago` = `eop`.`id`)))
        LEFT JOIN `adif_compras`.`proveedor` `p` ON ((`co`.`id_proveedor` = `p`.`id`)))
        LEFT JOIN `adif_contable`.`documento_financiero` `df` ON ((`df`.`id` = `co`.`id_documento_financiero`)))
        LEFT JOIN `adif_contable`.`obras_tramo` `ot` ON ((`ot`.`id` = `df`.`id_tramo`)))
    WHERE
        (ISNULL(`c`.`fecha_baja`)
            AND ISNULL(`op`.`fecha_baja`)
            AND (NOT (EXISTS( SELECT 
                1
            FROM
                `adif_contable`.`comprobante_ajuste` `ca`
            WHERE
                (`c`.`id` = `ca`.`id_comprobante`)))))