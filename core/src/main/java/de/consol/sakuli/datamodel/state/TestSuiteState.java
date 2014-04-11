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

package de.consol.sakuli.datamodel.state;

/**
 * Enum which represents the Sahi-Case-Stati in file "sahi_return_codes"
 *
 * @author tschneck
 *         Date: 21.06.13
 */
public enum TestSuiteState implements SakuliStateInterface {
    /**
     * value = 0
     */
    OK(0),

    /**
     * value = 1
     */
    WARNING_IN_STEP(1),

    /**
     * value = 2
     */
    WARNING_IN_CASE(2),

    /**
     * value = 3
     */
    WARNING_IN_SUITE(3),

    /**
     * value = 4
     */
    CRITICAL_IN_CASE(4),

    /**
     * value = 4
     */
    CRITICAL_IN_SUITE(5),

    /**
     * value = 6
     */
    ERRORS(6),

    /**
     * state during the execution
     */
    RUNNING(-1);
    private final int errorCode;

    private TestSuiteState(int i) {
        this.errorCode = i;
    }

    public int getErrorCode() {
        return errorCode;
    }

}