<!-- Company Information Schema -->

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Organization",
  "@id": "<?php echo SITE_URL ?>",
  "legalName" : "<?php echo SITE_NAME ?>",
  "description" : "<?php echo SITE_DESC ?>",
  "url" : "<?php echo SITE_URL ?>",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Paris, France",
    "postalCode": "F-75002",
    "streetAddress": "38 avenue de l'Opera"
  },
  "email": "secretariat(at)google.org",
  "name": "<?php echo SITE_NAME ?>",
  "telephone": "( 33 1) 42 68 53 00",
  "logo" : "<?php echo SITE_LOGO ?>",
  "sameAS" : [
  	"<?php echo SITE_URL ?>"
  ]
}
</script>

<!-- Search Box Schema -->

<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "url": "<?php echo SITE_URL ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?php echo SITE_URL ?>/search/?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
</script>