/*
* Sakuli - Testing and Monitoring-Tool for Websites and common UIs.
*
* Copyright 2013 - 2014 the original author or authors.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

/**** Exclude this global variables from JSLint Warnings ****/
/* global navigator, window, java, Packages,saveResult,step, $output, _set, _stopOnError, _logExceptionAsFailure,_resolvePath,_include, $sahi_userdata, $guid, $capture, initialize */


/*****************************************************************************************************
* Region Class
*
* @param optResumeOnException if this parameter is undefined, it will be false.
*****************************************************************************************************/

function Region(optResumeOnException) {
if (undefined == optResumeOnException) {
optResumeOnException = new Boolean(false);
}
return loadRegion(Packages.de.consol.sakuli.loader.BeanLoader.loadRegion(optResumeOnException), optResumeOnException);
}

function RegionImage(imageName, optResumeOnException) {
if (undefined == optResumeOnException) {
optResumeOnException = new Boolean(false);
}
return loadRegion(Packages.de.consol.sakuli.loader.BeanLoader.loadRegionImage(imageName, optResumeOnException), optResumeOnException);
};

function RegionRectangle(x, y, w, h, optResumeOnException) {
if (undefined == optResumeOnException) {
optResumeOnException = new Boolean(false);
}
return loadRegion(Packages.de.consol.sakuli.loader.BeanLoader.loadRegionRectangle(x, y, w, h, optResumeOnException), optResumeOnException);
};

function loadRegion(javaObject, resumeOnException) {
var that = {}, update;


/**
* Finds an image inside this region immediately.
*
* @param optImageName name of the preloaded picture
*        (if not set, the find operation will take place on the predefined region object.)
* @return the found {@link Region} or if the target can't be found {@code null}.
*/
that.find = function (optImageName) {
if (undefined == optImageName) {
return update(that.javaObject.find());
}
return update(that.javaObject.find(optImageName));
};

/**
* Finds a target in this {@link Region} immediately;
*
* @return the found {@link Region} or if the target can't be found {@code null}.
*/
that.findRegion = function (region) {
return update(that.javaObject.findRegion(region));
};


/**************************************
* EXISTS FUNCTIONS
*************************************/
/**
* Check whether the give pattern is visible on the screen.
*
* @param optImageName if set, the function search inside the given region for the image
* @param optWaitSeconds if set, the funciton search for x seconds for the pattern.
* @return this {@link Region} or null
*/
that.exists = function (optImageName, optWaitSeconds) {
if (undefined == optImageName && undefined == optWaitSeconds) {
return update(that.javaObject.exists());
} else if (undefined == optWaitSeconds) {
return update(that.javaObject.exists(optImageName));
}
return update(that.javaObject.exists(optImageName, optWaitSeconds));
};


/**
* makes a mouse click on the center of the {@link Region}.
*
* @return the {@link Region} or NULL on errors.
*/
that.click = function () {
return update(that.javaObject.click());
};

/**
* makes a double click on the center of the {@link Region}.
*
* @return the {@link Region} or NULL on errors.
*/
that.doubleClick = function () {
return update(that.javaObject.doubleClick());
};


/**
* makes a rigth click on the center of the {@link Region}.
*
* @return the {@link Region} or NULL on errors.
*/
that.rightClick = function () {
return update(that.javaObject.rightClick());
};


/**
* Blocks and waits until a target which is specified by the optImageName is found in the hole
* {@link Screen} within a given time period in seconds.
*
* @param imageName name of the image pattern
* @param seconds   the maximum time to waitFor in seconds
* @return a {@link Region} object representing the region occupied by the found target,
*         or null if the target can not be found within the given time.
*/
that.waitForImage = function (imageName, seconds) {
return update(that.javaObject.waitForImage(imageName, seconds));
};

/**
* same function as {@link #waitForImage(String, int)}, just waiting for the predefined pattern.
*
* @param seconds the maximum time to waitFor in seconds
* @return a {@link Region} object representing the region occupied by the found target,
*         or null if the target can not be found within the given time.
*/
that.waitFor = function (seconds) {
return update(that.javaObject.waitFor(seconds));
};

/**
* pastes the text at the current position of the focus/carret <br/>using the
* clipboard and strg/ctrl/cmd-v (paste keyboard shortcut)
*
* @param text a string, which might contain unicode characters
* @return this {@link Region} or NULL on errors.
*/
that.paste = function (text) {
return update(that.javaObject.paste(text));
};


/**
* makes a masked {@link #paste(String)} without any logging.
*
* @param text a string, which might contain unicode characters
* @return this {@link Region} or NULL on errors.
*/
that.pasteMasked = function (text) {
return update(that.javaObject.pasteMasked(text));
};

/**
* combines {@link #pasteMasked(String)} and {@link #decryptSecret(String)}.
*
* @param text encrypted secret
* @return this {@link Region} or NULL on errors.
*/
that.pasteAndDecrypt = function (text) {
return update(that.javaObject.pasteAndDecrypt(text));
};

/**
* Enters the given text one character/key after another using keyDown/keyUp.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Region} or NULL on errors.
*/
that.type = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.type(text));
}
return update(that.javaObject.type(text, optModifiers));
};

/**
* Enters the given text one character/key after another using keyDown/keyUp.
* The entered text will be masked at the logging.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Region} or NULL on errors.
*/
that.typeMasked = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.typeMasked(text));
}
return update(that.javaObject.typeMasked(text, optModifiers));
};


/**
* Decrypt and enters the given text one character/key after another using keyDown/keyUp.
* The entered text will be masked at the logging. For the deatails of the decryption see {@link #decryptSecret(String)}.
* <p/>
* About the usable Key constants see documentation of {@link Key}.
* The function use a subset of a US-QWERTY PC keyboard layout to type the text.
* The text is entered at the current position of the focus.
*
* @param text containing characters and/or {@link Key} constants
* @param optModifiers (optional) an String with only {@link Key} constants.
* @return this {@link Region} or NULL on errors.
*/
that.typeAndDecrypt = function (text, optModifiers) {
if (undefined == optModifiers) {
return update(that.javaObject.typeAndDecrypt(text))
}
return update(that.javaObject.typeAndDecrypt(text, optModifiers));
};

/**
* delete a amount of chars in a field
*
* @param amountOfChars number of chars to delete
* @return this {@link Region} or null on errors
*/
that.deleteChars = function (amountOfChars) {
return update(that.javaObject.deleteChars(amountOfChars));
};


/*********************
* MOUSE WHEEL FUNCTIONS
*********************/
/**
* move the mouse pointer to the given target location and move the
* wheel the given steps down.
*
* @param steps the number of steps
*/
that.mouseWheelDown = function (steps) {
return update(that.javaObject.mouseWheelDown(steps));
};

/**
* move the mouse pointer to the given target location and move the
* wheel the given steps up.
*
* @param steps the number of steps
*/
that.mouseWheelUp = function (steps) {
return update(that.javaObject.mouseWheelUp(steps));
};


/**
* Set a offset to a specific {@link Region} and returns the new {@link Region} object.
* The offset function will move the Region's rectangle with x to the right and with y to the left.
* The size of the rectangle will be the same.
*
* @param offsetX x-value for the offset action
* @param offsetY y-value for the offset action
* @return a {@link Region} with the new coordinates
*/
that.move = function (offsetX, offsetY) {
return update(that.javaObject.move(offsetX, offsetY));
};

/**
* create a region enlarged range pixels on each side
*
* @param range of pixels
* @return a new {@link Region}
*/
that.grow = function (range) {
return update(that.javaObject.grow(range));
}

/**
* create a region with enlarged range pixels
*
* @param width  in pixels to grow in both directions
* @param height in pixels to grow in both directions
* @return a new {@link Region}
*/
that.grow = function (width, height) {
if (undefined == height) {
//prevents js wrong function matching
return update(that.javaObject.grow(width));
}
return update(that.javaObject.grow(width, height));
}

/**
* @return  a new {@link Region} that is defined above the current region’s top border
* with a height of range number of pixels.
*/
that.above = function (range) {
return update(that.javaObject.above(range));
}

/**
* @return  a new {@link Region} that is defined below the current region’s top border
* with a height of range number of pixels.
*/
that.below = function (range) {
return update(that.javaObject.below(range));
}

/**
* @return  a new {@link Region} that is defined on the left the current region’s top border
* with a height of range number of pixels.
*/
that.left = function (range) {
return update(that.javaObject.left(range));
}

/**
* @return  a new {@link Region} that is defined on the right the current region’s top border
* with a height of range number of pixels.
*/
that.right = function (range) {
return update(that.javaObject.right(range));
}


/**
* set the height, based form the upper left corner downsides
*/
that.setH = function (height) {
return update(that.javaObject.setH(height));
};

/**
* @return height as int value
*/
that.getH = function () {
return that.javaObject.getH();
};

/**
* set the width, based form the upper left corner to the right
*/
that.setW = function (width) {
return update(that.javaObject.setW(width));
};

/**
* @return width as int value
*/
that.getW = function () {
return that.javaObject.getW();
};

/**
* set the X coordinate of the upper left corner.
*/
that.setX = function (x) {
return update(that.javaObject.setX(x));
};

/**
* @return width as int value
*/
that.getX = function () {
return that.javaObject.getX();
};

/**
* @return X coordinate of the upper left corner
*/
that.setY = function (y) {
return update(that.javaObject.setY(y));
};

/**
* @return Y coordinate of the upper left corner
*/
that.getY = function () {
return that.javaObject.getY();
};

/**
* highlights this {@link Region} for x seconds
* or the default time
*/
that.highlight = function (seconds) {
if (undefined == seconds) {
return update(that.javaObject.highlight());
}
return update(that.javaObject.highlight(seconds));
};

/**
* @return from this region a extracted Text as {@link String}
*/
that.extractText = function () {
return that.javaObject.extractText();
};


/**
* gets the inherit java object for not yet wrapped methods
* @returns {*}
*/
that.getRegionImpl = function () {
return that.javaObject.getRegionImpl();
}

/**
* updats the inherit java object after modifaction with {@link #getRegionImpl}.
* @returns {*}
*/
that.setRegionImpl = function (modifiedJavaObject) {
that.javaObject.setRegionImpl(modifiedJavaObject);
return that;
}

/*****************************************************************************************************
* INTERNAL CLASS FUNCTIONS - NOT REACHABLE IN THE TEST CASE EXECUTION
*****************************************************************************************************/

that.javaObject = javaObject;
that.resumeOnException = new Boolean(resumeOnException);

update = function (updatedJavaObject) {
if (undefined == updatedJavaObject || updatedJavaObject == null) {
return undefined;
}
return new loadRegion(updatedJavaObject, that.resumeOnException);
};

return that;
};


