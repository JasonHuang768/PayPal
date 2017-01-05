
CREATE TABLE IF NOT EXISTS `paypal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paymentId` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `PayerID` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;


ALTER TABLE order add token text NOT NULL;
