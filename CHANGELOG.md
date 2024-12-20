# Changelog

## [0.9.0](https://github.com/cycle/active-record/compare/v0.8.0...v0.9.0) (2024-12-20)


### Features

* add AR::transact() method to open a DB transaction; ([be62b85](https://github.com/cycle/active-record/commit/be62b85a503ad5cc818e6d3046ee14c9e7d55353))
* rename ActiveRecord::transact to ActiveRecord::groupActions ([2e0aa92](https://github.com/cycle/active-record/commit/2e0aa92915e82521aa511b707d47af068613c2c7))
* send `EntityManagerInterface` as the first argument of `AR::groupActions()` callable ([e2036f3](https://github.com/cycle/active-record/commit/e2036f392daf69e9bbebac2633b9ed6559465f7a))

## [0.8.0](https://github.com/cycle/active-record/compare/v0.7.0...v0.8.0) (2024-07-24)


### Features

* implement AR transaction via UoW ([9b55326](https://github.com/cycle/active-record/commit/9b553264043c1d77a84b168c3cfc8546f17299f0))
* rewrite AR transaction to using EntityManager ([10dea8f](https://github.com/cycle/active-record/commit/10dea8ff5f2802db69e309ccd3db094bc433ddab))

## [0.7.0](https://github.com/cycle/active-record/compare/v0.6.0...v0.7.0) (2024-07-04)


### Features

* add `AR::make()` method ([a5a37fe](https://github.com/cycle/active-record/commit/a5a37feefdeba121d65f3545e0856835d8d1b5cc))
* unfinalize `ActiveQuery::__construct()` and `ActiveRepository::__construct()`; ([ad7c0de](https://github.com/cycle/active-record/commit/ad7c0dec619762b8bad82803c666e511e09aea88))
* update `ActiveRepository` to have all the necessary methods; add examples ([1031c38](https://github.com/cycle/active-record/commit/1031c3895533a5b9d16300d4d2140c5ae55253fa))


### Documentation

* Andrij's Jul 3 changes [19] ([9dbb0f7](https://github.com/cycle/active-record/commit/9dbb0f7f60933225e4239a1876df60658e6c681b))
* Andrij's Jul 3 changes [20] ([b52b819](https://github.com/cycle/active-record/commit/b52b8194c043feb73f367224e5f71c337b03d488))
* docs: fix link [23] ([fcc8b4c](https://github.com/cycle/active-record/commit/fcc8b4c0b641fec082e36d534558de4205158839))
* minor fixes in docs [21] ([3cb2bc0](https://github.com/cycle/active-record/commit/3cb2bc015eb1ec934472c7bb0c348c9c981c9e49))

## [0.6.0](https://github.com/cycle/active-record/compare/v0.5.0...v0.6.0) (2024-06-11)


### Features

* **ActiveQuery:** orm is available as protected property of class ([92c65f4](https://github.com/cycle/active-record/commit/92c65f4c6a84112fa772a5c691081be5f780df9f))
* rename active record method (find =&gt; findByPK); add type annotations ([58d85ca](https://github.com/cycle/active-record/commit/58d85ca5afdebf183067e0efcf51f299b207f70d))
* rename active repository methods; add type annotations ([485848b](https://github.com/cycle/active-record/commit/485848b09f83f0e757b08e343a2f0f81b86ae142))


### Bug Fixes

* psalm failure and markdown failure with docs dir ([956618d](https://github.com/cycle/active-record/commit/956618dc9db40da396508f52cf3824d5e3ba1b45))
* use self accessor, as test classes are final ([b0b0eb1](https://github.com/cycle/active-record/commit/b0b0eb128fbdd43de8f395f716677c261387ec6b))


### Documentation

* add initial CONTRIBUTING.md ([6e5cf81](https://github.com/cycle/active-record/commit/6e5cf81de3447d39cfe4836a0aad6f03ddb37f85))
* No subject [13] ([9f4b397](https://github.com/cycle/active-record/commit/9f4b3972b7f1d7e507a2a2003b08ac3a7b12b00a))
* No subject [16] ([7cc6ab3](https://github.com/cycle/active-record/commit/7cc6ab35da9ba0e7f80b96dd82d3ce4c2937681f))
* update CONTRIBUTING.md guidelines ([409cf4e](https://github.com/cycle/active-record/commit/409cf4ec4ee9a2b98ef0bb04b8b1814e3acaeee2))

## [0.5.0](https://github.com/cycle/active-record/compare/v0.4.0...v0.5.0) (2024-05-05)


### Features

* ActiveQuery PoC ([502fa02](https://github.com/cycle/active-record/commit/502fa02202f31f253e5a7a279832a52486911ade))
* Add ActiveRepository draft ([35e496b](https://github.com/cycle/active-record/commit/35e496b10b2218e5da22d88a018f20c0ac301d1e))
* add repository method for active-record class ([b3aef78](https://github.com/cycle/active-record/commit/b3aef78f4a5317240391d65c6e34349ccf1d4d34))
* set repository namespace from wayofdev to cycle ([918a1df](https://github.com/cycle/active-record/commit/918a1df419df7077ea33b47c0ab2fd0f1de8b46d))
* use ActiveQuery class in ActiveRecord ([350b9f5](https://github.com/cycle/active-record/commit/350b9f5007030befed2b80b63d42c6ccaa17419b))


### Bug Fixes

* generic types ([3e74968](https://github.com/cycle/active-record/commit/3e749683507f34f69c449c600bce70ac9dd4ed5d))
* generics in repository ([ba41883](https://github.com/cycle/active-record/commit/ba41883ae151ad6548eb9f82468b3cb2ebe3ffa2))

## [0.4.0](https://github.com/wayofdev/active-record/compare/v0.3.0...v0.4.0) (2024-05-03)


### Features

* use custom exceptions ([e18df6d](https://github.com/wayofdev/active-record/commit/e18df6de1265546fe516b720f9272bbcffedeb85))

## [0.3.0](https://github.com/wayofdev/active-record/compare/v0.2.1...v0.3.0) (2024-05-03)


### Features

* add delete entity method ([8be8e30](https://github.com/wayofdev/active-record/commit/8be8e305e19b5f00ea075273e6ec2eb4d466f8b9))
* add persist method and tests ([abd9ff1](https://github.com/wayofdev/active-record/commit/abd9ff1d6dd6733d885e61f5513c089302c83065))
* add remove method ([4d2843c](https://github.com/wayofdev/active-record/commit/4d2843c3685e9f35fe61b784fd62a4b512707cd2))
* allow to save entities ([ffdd41a](https://github.com/wayofdev/active-record/commit/ffdd41adda638d5dea3516029bcef0c604d9d193))


### Documentation

* add basic example ([4e9d2e5](https://github.com/wayofdev/active-record/commit/4e9d2e54444afa2dec5de3e958bbea1bad5217c0))
* fix type coverage badge ([83f952d](https://github.com/wayofdev/active-record/commit/83f952df2c391633e4873ed82103791244baebe6))
* update readme description ([ccc72ca](https://github.com/wayofdev/active-record/commit/ccc72cab99284457d00a31988dd2e0a7cae36d7b))

## [0.2.1](https://github.com/wayofdev/active-record/compare/v0.2.0...v0.2.1) (2024-05-02)


### Documentation

* update readme ([852affd](https://github.com/wayofdev/active-record/commit/852affda822ce8101819407c40d8d5a7229b96e8))
* updating badges ([c6aea4c](https://github.com/wayofdev/active-record/commit/c6aea4c3b02a7b494d3620954889eb58c90fdbe9))

## [0.2.0](https://github.com/wayofdev/active-record/compare/v0.1.0...v0.2.0) (2024-05-02)


### Features

* added buggregator/trap support with Makefile and docker ([9a0de71](https://github.com/wayofdev/active-record/commit/9a0de7197c63cecb68460672f9dbce24d5db5bc0))
* code updates ([9a0de71](https://github.com/wayofdev/active-record/commit/9a0de7197c63cecb68460672f9dbce24d5db5bc0))


### Bug Fixes

* failing static facade tests ([6f72a44](https://github.com/wayofdev/active-record/commit/6f72a448dd345cd5d2d5a4baf6b1410855c136d2))
* laravel service provider ([9a0de71](https://github.com/wayofdev/active-record/commit/9a0de7197c63cecb68460672f9dbce24d5db5bc0))
* phpstan errors on level 5 ([9a0de71](https://github.com/wayofdev/active-record/commit/9a0de7197c63cecb68460672f9dbce24d5db5bc0))
* psalm and infection checks ([ba78f31](https://github.com/wayofdev/active-record/commit/ba78f31edbdcc3800a3ea57bb3d708ec8e8c277f))
* require dependencies ([d88008e](https://github.com/wayofdev/active-record/commit/d88008e315f1479a657980a571601edd5fe5cfc7))


### Documentation

* add initial readme ([25fd256](https://github.com/wayofdev/active-record/commit/25fd2563e291c6e9fe2162274b8662231bb529b3))
* readme updates ([5f83cfc](https://github.com/wayofdev/active-record/commit/5f83cfc58bc0672e518ae68ecf86deacded48084))
* update readme ([0cb8374](https://github.com/wayofdev/active-record/commit/0cb837475719c7ce9f2d23654711f71a55e49865))
* update readme ([9aebaff](https://github.com/wayofdev/active-record/commit/9aebaffa4cd5bebc83057c84cc17faaa34de1716))


### Dependencies

* require laravel support package as dev package ([908ace0](https://github.com/wayofdev/active-record/commit/908ace0a6e54e2d45431447a887c9aa718c6f214))
