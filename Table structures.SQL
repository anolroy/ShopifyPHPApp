-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 213.171.200.21    Database: ShopifyTestDB_1
-- ------------------------------------------------------
-- Server version	5.7.44-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AppSettings`
--

DROP TABLE IF EXISTS `AppSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `AppSettings` (
  `CustomerId` bigint(20) NOT NULL,
  `WarehouseId` bigint(20) NOT NULL,
  `PriceBandId` bigint(20) NOT NULL,
  `AdditionalChargeId` bigint(20) NOT NULL,
  `VatId` bigint(20) NOT NULL,
  `PaymentMethodId` bigint(20) NOT NULL,
  `BankId` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Receipt`
--

DROP TABLE IF EXISTS `Receipt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Receipt` (
  `id` varchar(45) NOT NULL,
  `order_id` varchar(250) DEFAULT NULL,
  `currency` varchar(50) DEFAULT NULL,
  `payment_id` varchar(250) NOT NULL,
  `type` int(3) DEFAULT NULL,
  `TransactionTypeDesc` varchar(45) DEFAULT NULL,
  `SourcePHP` varchar(245) DEFAULT NULL,
  `SourceID` varchar(45) DEFAULT NULL,
  `gateway` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `message` text,
  `TotalTax` decimal(15,6) DEFAULT '0.000000',
  `amount` decimal(10,2) NOT NULL,
  `ReceiptDownloaded` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Shippingdetails`
--

DROP TABLE IF EXISTS `Shippingdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Shippingdetails` (
  `ShippingChargeID` bigint(20) NOT NULL,
  `OrderHeaderId` bigint(20) DEFAULT NULL,
  `OrderNumber` varchar(20) DEFAULT NULL,
  `TransactionType` int(3) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `ChargeAmount` decimal(15,6) DEFAULT NULL,
  `TaxAmount` decimal(15,6) DEFAULT NULL,
  PRIMARY KEY (`ShippingChargeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fulfillment`
--

DROP TABLE IF EXISTS `fulfillment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fulfillment` (
  `ID` varchar(255) DEFAULT NULL,
  `rowID` varchar(45) DEFAULT NULL,
  `InvoiceID` varchar(255) DEFAULT NULL,
  `fulfillmentNo` varchar(45) DEFAULT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `downloaded` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hooklog`
--

DROP TABLE IF EXISTS `hooklog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hooklog` (
  `id` varchar(25) DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `datecalled` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orderdetails`
--

DROP TABLE IF EXISTS `orderdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orderdetails` (
  `uniqueID` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `OrderDetailsId` int(11) DEFAULT NULL,
  `OrderHeaderId` varchar(20) COLLATE utf8_bin DEFAULT '0',
  `OrderNumber` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `TransactionType` int(3) DEFAULT NULL,
  `TransactionTypeDesc` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `ProductId` bigint(20) DEFAULT NULL,
  `StockCode` varchar(66) COLLATE utf8_bin DEFAULT '',
  `Description` varchar(50) COLLATE utf8_bin DEFAULT '',
  `UnitNettPrice` decimal(15,6) DEFAULT '0.000000',
  `ReturnReason` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `QuantitySold` decimal(15,6) DEFAULT '0.000000',
  `LineNettValue` decimal(15,6) DEFAULT '0.000000',
  `LineVatValue` decimal(15,6) DEFAULT '0.000000',
  `VatCode` varchar(1) COLLATE utf8_bin DEFAULT '',
  `vatRate` decimal(6,2) DEFAULT '0.00',
  `LocationCode` varchar(20) COLLATE utf8_bin DEFAULT '',
  `Model` varchar(50) COLLATE utf8_bin DEFAULT '',
  `Discount` decimal(15,2) DEFAULT '0.00',
  `PriceOrDiscount` varchar(1) COLLATE utf8_bin DEFAULT '',
  `ErrorReturnText` varchar(255) COLLATE utf8_bin DEFAULT '',
  `CreationDateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `LastModifiedDateTime` datetime DEFAULT '1900-01-01 00:00:00',
  `LineDiscountValue` decimal(15,6) DEFAULT '0.000000',
  `PriceRefund` varchar(50) COLLATE utf8_bin DEFAULT '',
  `FulfilledQty` int(11) DEFAULT NULL,
  `taxable` varchar(1) COLLATE utf8_bin DEFAULT NULL,
  `tax_lines` decimal(10,2) DEFAULT NULL,
  `target_type` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `value` decimal(15,6) DEFAULT NULL,
  `value_type` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `allocation_method` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `target_selection` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  KEY `Code` (`OrderDetailsId`,`OrderHeaderId`)
) ENGINE=MyISAM AUTO_INCREMENT=2147483648 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orderheaders`
--

DROP TABLE IF EXISTS `orderheaders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orderheaders` (
  `OrderHeaderId` varchar(20) COLLATE utf8_bin NOT NULL,
  `OrderNumber` varchar(20) COLLATE utf8_bin DEFAULT '',
  `TransactionType` int(3) DEFAULT NULL,
  `TransactionTypeDesc` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `OrderDate` date DEFAULT '1900-01-01',
  `InvoiceName` varchar(40) COLLATE utf8_bin DEFAULT '',
  `company` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `InvoiceAddress1` varchar(50) COLLATE utf8_bin DEFAULT '',
  `InvoiceAddress2` varchar(50) COLLATE utf8_bin DEFAULT '',
  `InvoiceAddress3` varchar(50) COLLATE utf8_bin DEFAULT '',
  `InvoiceAddress4` varchar(50) COLLATE utf8_bin DEFAULT '',
  `InvoiceAddress5` varchar(50) COLLATE utf8_bin DEFAULT '',
  `InvoicePostcode` varchar(8) COLLATE utf8_bin DEFAULT '',
  `DeliveryName` varchar(40) COLLATE utf8_bin DEFAULT '',
  `DeliveryAddress1` varchar(50) COLLATE utf8_bin DEFAULT '',
  `DeliveryAddress2` varchar(50) COLLATE utf8_bin DEFAULT '',
  `DeliveryAddress3` varchar(50) COLLATE utf8_bin DEFAULT '',
  `DeliveryAddress4` varchar(50) COLLATE utf8_bin DEFAULT '',
  `DeliveryAddress5` varchar(50) COLLATE utf8_bin DEFAULT '',
  `DeliveryPostcode` varchar(8) COLLATE utf8_bin DEFAULT '',
  `DeliveryTelephone_number` varchar(40) COLLATE utf8_bin DEFAULT '',
  `DeliveryFax_number` varchar(40) COLLATE utf8_bin DEFAULT '',
  `EmailAddress` varchar(250) COLLATE utf8_bin DEFAULT '',
  `SalesLedgerAccountCode` varchar(10) COLLATE utf8_bin DEFAULT '',
  `Comments` text COLLATE utf8_bin,
  `CustomerReferenceNumber` varchar(20) COLLATE utf8_bin DEFAULT '',
  `ShippingMethod` varchar(128) COLLATE utf8_bin DEFAULT '',
  `PaymentWithOrder` int(11) DEFAULT '0',
  `PaymentMethod` varchar(128) COLLATE utf8_bin DEFAULT '',
  `LocationCode` varchar(20) COLLATE utf8_bin DEFAULT '',
  `CurrencyCode` varchar(3) COLLATE utf8_bin DEFAULT '',
  `UserField1` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField2` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField3` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField4` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField5` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField6` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField7` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField8` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField9` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField10` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField11` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField12` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField13` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField14` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField15` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField16` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField17` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField18` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField19` varchar(30) COLLATE utf8_bin DEFAULT '',
  `UserField20` varchar(30) COLLATE utf8_bin DEFAULT '',
  `ExchangeRate` decimal(15,6) DEFAULT '1.000000',
  `Paymentcode1` varchar(20) COLLATE utf8_bin DEFAULT '',
  `PaymentValue1` decimal(15,6) DEFAULT '0.000000',
  `Paymentcode2` varchar(20) COLLATE utf8_bin DEFAULT '',
  `PaymentValue2` decimal(15,6) DEFAULT '0.000000',
  `Paymentcode3` varchar(20) COLLATE utf8_bin DEFAULT '',
  `PaymentValue3` decimal(15,6) DEFAULT '0.000000',
  `Paymentcode4` varchar(20) COLLATE utf8_bin DEFAULT '',
  `PaymentValue4` decimal(15,6) DEFAULT '0.000000',
  `Paymentcode5` varchar(20) COLLATE utf8_bin DEFAULT '',
  `PaymentValue5` decimal(15,6) DEFAULT '0.000000',
  `DiscountPercentage` decimal(6,2) DEFAULT '0.00',
  `TagNumber` decimal(2,0) DEFAULT '0',
  `CreationDateTime` datetime DEFAULT CURRENT_TIMESTAMP,
  `LastModifiedDateTime` datetime DEFAULT '1900-01-01 00:00:00',
  `DeliveryDateEarliest` date DEFAULT '1900-01-01',
  `OrderCompleted` int(11) DEFAULT '0',
  `OrderReturn` int(11) DEFAULT '0',
  `PaymentRefund` decimal(15,6) DEFAULT '0.000000',
  `Fulfilled` varchar(50) COLLATE utf8_bin DEFAULT '0',
  `OrderGrossTotal` decimal(15,6) DEFAULT '0.000000',
  `OrderAmount` decimal(15,6) DEFAULT '0.000000',
  `shippingCharge` decimal(15,6) DEFAULT NULL,
  `shipping_tax` decimal(10,2) DEFAULT NULL,
  `total_tax` decimal(10,2) DEFAULT NULL,
  `financial_status` varchar(120) COLLATE utf8_bin DEFAULT NULL,
  `OrderDiscount` decimal(15,6) DEFAULT '0.000000',
  `Code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `Amount` decimal(15,6) DEFAULT NULL,
  `Type` varchar(45) COLLATE utf8_bin DEFAULT '',
  `target_type` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `value` decimal(15,6) DEFAULT '0.000000',
  `allocation_method` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `target_selection` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `sourcePHP` varchar(245) COLLATE utf8_bin DEFAULT NULL,
  `RecordDownloaded` int(11) DEFAULT '0',
  PRIMARY KEY (`OrderHeaderId`),
  KEY `Code` (`OrderNumber`),
  KEY `RecordDownloaded` (`RecordDownloaded`)
) ENGINE=MyISAM AUTO_INCREMENT=1730379397 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockrecords`
--

DROP TABLE IF EXISTS `stockrecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stockrecords` (
  `StockId` int(11) NOT NULL,
  `StockCode` varchar(66) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ProductId` bigint(20) NOT NULL,
  `variant_id` bigint(20) NOT NULL,
  `Description` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `taxable` varchar(1) COLLATE utf8_bin DEFAULT NULL,
  `CategoryCodes` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `SellPrice` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPricea` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPriceb` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPricec` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPriced` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPricee` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPricef` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPriceg` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SellPriceh` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `CostPrice` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `UnitDescription` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ExtendedDescription` text COLLATE utf8_bin NOT NULL,
  `UserField1` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField2` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField3` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField4` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField5` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField6` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField7` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField8` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField9` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField10` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField11` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField12` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField13` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField14` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField15` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField16` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField17` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField18` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField19` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `UserField20` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `FeaturedItem` int(11) NOT NULL DEFAULT '0',
  `NonSaleableOnWeb` int(11) NOT NULL DEFAULT '0',
  `Weight` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `ImageNames` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `VatCode` varchar(1) COLLATE utf8_bin NOT NULL DEFAULT '',
  `SellPricevatValue` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `SearchRef1` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `SearchRef2` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `FreeStockQuantity` decimal(15,6) NOT NULL DEFAULT '0.000000',
  `ComponentOnly` int(11) NOT NULL DEFAULT '0',
  `MetaTitle` varchar(252) COLLATE utf8_bin NOT NULL DEFAULT '',
  `MetaKeywords` text COLLATE utf8_bin NOT NULL,
  `DiscountCode` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `CustomText1` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
  `CustomText2` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
  `CustomText3` varchar(75) COLLATE utf8_bin NOT NULL DEFAULT '',
  `customDate1` date NOT NULL DEFAULT '1900-01-01',
  `customDate2` date NOT NULL DEFAULT '1900-01-01',
  `AnalysisCode` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `CompareStatus` tinyint(4) NOT NULL DEFAULT '0',
  `RecordUpdated` int(11) NOT NULL DEFAULT '0',
  `CreationDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `LastModifiedDateTime` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  KEY `Code` (`StockCode`)
) ENGINE=MyISAM AUTO_INCREMENT=8863 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-23 10:56:24
