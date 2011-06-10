
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $q = $this->get('doctrine')->getEntityManager()->createQuery('SELECT o FROM {{ bundle }}:{{ entity_class }} o');

        return array('objects' => $q->getResult());
    }
