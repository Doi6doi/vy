<?php

namespace vy;

/// Repo-ban tárolható elem
interface RepoItem {
   
   /// csomag neve
   function pkgName();
   
   /// verzió
   function ver();
   
}
