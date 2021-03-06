Introduction to containers and scopes
=====================================

In order to provide support for defining contexts and pages in Behat container with dependencies from Symfony application
container, our service definitions may contain some extra features.

There are 3 available containers:

  - ``behat`` (the default one) - the container which holds all Behat services, its extensions services, our contexts,
    pages and helper services used by them

  - ``symfony`` - the container which holds all the services defined in the application, the services retrieved from this
    container are isolated between scenarios, belongs to ``scenario`` scope

  - ``symfony_shared`` - the container which holds all the services defined in the application, created only once,
    the services retrieved from this container are not isolated between scenarios

There is one additional scope, ``scenario``, which ensures, that no service shared through entire Behat execution uses
the one that is only available in specific scenario. Every service retrieved from ``symfony`` container has this scope.

Usually, most of contexts and pages are defined in ``scenario`` scope as it's the most safe decision. You would need a
really good reason not to follow this convention.

Right now, you can only inject services from foreign containers into the default containers. To do so, use ``container``
option within ``argument`` element:

.. code-block:: xml

    <service id="service.id" class="Class">
        <argument type="service" id="behat.service.id" />
        <argument type="service" id="another.behat.service.id" container="behat" />
        <argument type="service" id="symfony.service.id" container="symfony" /> <!-- to use this container, your service must be in scenario scope too -->
        <argument type="service" id="shared.symfony.service.id" container="symfony_shared" />
    </service>
